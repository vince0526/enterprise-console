<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\SavedView;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SavedViewsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_crud_saved_views(): void
    {
        /** @var User&\Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create();

        // List empty
        $resp = $this->actingAs($user)
            ->getJson(route('emc.core.saved-views.index'));
        $resp->assertOk()->assertExactJson([]);
        $resp->assertHeader('X-SavedViews-Total', '0');
        $resp->assertHeader('X-SavedViews-Limit', '50');
        $resp->assertHeader('X-SavedViews-Returned', '0');

        // Create
        $create = $this->actingAs($user)
            ->postJson(route('emc.core.saved-views.store'), [
                'name' => 'My Prod PG',
                'context' => 'core_databases',
                'filters' => [
                    'engine' => 'PostgreSQL',
                    'env' => 'Prod',
                ],
            ])
            ->assertCreated()
            ->json();

        $this->assertArrayHasKey('id', $create);
        $id = $create['id'];

        // List shows one
        $resp = $this->actingAs($user)
            ->getJson(route('emc.core.saved-views.index'));
        $resp->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['name' => 'My Prod PG']);
        $resp->assertHeader('X-SavedViews-Total', '1');
        $resp->assertHeader('X-SavedViews-Limit', '50');
        $resp->assertHeader('X-SavedViews-Returned', '1');

        // Update (same name -> upsert)
        $this->actingAs($user)
            ->postJson(route('emc.core.saved-views.store'), [
                'name' => 'My Prod PG',
                'context' => 'core_databases',
                'filters' => [
                    'engine' => 'PostgreSQL',
                    'env' => 'Dev',
                ],
            ])
            ->assertCreated();

        $this->assertSame(1, SavedView::query()->count());
        $this->assertSame('Dev', SavedView::query()->first()->filters['env']);

        // Delete
        $this->actingAs($user)
            ->deleteJson(route('emc.core.saved-views.destroy', $id))
            ->assertOk();

        $this->assertSame(0, SavedView::query()->count());
    }

    public function test_search_and_limit_and_isolation(): void
    {
        /** @var User&\Illuminate\Contracts\Auth\Authenticatable $alice */
        $alice = User::factory()->create();
        /** @var User&\Illuminate\Contracts\Auth\Authenticatable $bob */
        $bob = User::factory()->create();

        // Seed multiple views for Alice
        foreach (['Alpha Prod', 'Alpha Dev', 'Beta Prod', 'Gamma'] as $name) {
            SavedView::factory()->create([
                'user_id' => $alice->id,
                'context' => 'core_databases',
                'name' => $name,
                'filters' => ['engine' => 'PostgreSQL'],
            ]);
        }
        // Bob gets one view with similar name to ensure isolation
        SavedView::factory()->create([
            'user_id' => $bob->id,
            'context' => 'core_databases',
            'name' => 'Alpha Staging',
            'filters' => ['env' => 'UAT'],
        ]);

        // Alice searches 'Alpha' limited to 2
        $resp = $this->actingAs($alice)
            ->getJson(route('emc.core.saved-views.index', ['q' => 'Alpha', 'limit' => 2]));
        $resp->assertOk();
        $data = $resp->json();
        $this->assertCount(2, $data); // limited
        $resp->assertHeader('X-SavedViews-Total', '2');
        $resp->assertHeader('X-SavedViews-Limit', '2');
        $resp->assertHeader('X-SavedViews-Returned', '2');
        $names = array_column($data, 'name');
        $this->assertNotContains('Alpha Staging', $names); // Bob's view excluded

        // Bob lists his (should only see his one)
        $this->actingAs($bob)
            ->getJson(route('emc.core.saved-views.index'))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['name' => 'Alpha Staging']);
    }

    public function test_validation_and_unauthorized_delete(): void
    {
        /** @var User&\Illuminate\Contracts\Auth\Authenticatable $alice */
        $alice = User::factory()->create();
        /** @var User&\Illuminate\Contracts\Auth\Authenticatable $bob */
        $bob = User::factory()->create();

        // Missing filters should 422
        $this->actingAs($alice)
            ->postJson(route('emc.core.saved-views.store'), [
                'name' => 'Invalid',
                'context' => 'core_databases',
            ])
            ->assertStatus(422);

        // Valid create
        $view = $this->actingAs($alice)
            ->postJson(route('emc.core.saved-views.store'), [
                'name' => 'Owned',
                'filters' => ['engine' => 'MySQL'],
            ])
            ->assertCreated()
            ->json();

        // Bob attempts delete -> 403
        $this->actingAs($bob)
            ->deleteJson(route('emc.core.saved-views.destroy', $view['id']))
            ->assertStatus(403);
    }

    public function test_limit_capped_and_negative_defaults(): void
    {
        /** @var User&\Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create();
        // create 130 views
        SavedView::factory()->count(130)->create(['user_id' => $user->id]);
        // limit >100 should cap at 100
        $resp = $this->actingAs($user)
            ->getJson(route('emc.core.saved-views.index', ['limit' => 1000]));
        $resp->assertOk()->assertJsonCount(100);
        $resp->assertHeader('X-SavedViews-Limit', '100');
        $resp->assertHeader('X-SavedViews-Returned', '100');
        $this->assertSame('130', $resp->headers->get('X-SavedViews-Total'));
        // negative limit -> default (50)
        $resp = $this->actingAs($user)
            ->getJson(route('emc.core.saved-views.index', ['limit' => -5]));
        $resp->assertOk()->assertJsonCount(50);
        $resp->assertHeader('X-SavedViews-Limit', '50');
        $resp->assertHeader('X-SavedViews-Returned', '50');
    }

    public function test_pagination_link_headers_next_and_prev(): void
    {
        /** @var User&\Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create();
        // Seed 75 views to ensure multiple pages
        SavedView::factory()->count(75)->create(['user_id' => $user->id, 'context' => 'core_databases']);
        // First page: limit 30, expect Link: rel="next" only
        $resp1 = $this->actingAs($user)
            ->getJson(route('emc.core.saved-views.index', ['limit' => 30, 'offset' => 0]));
        $resp1->assertOk()->assertJsonCount(30);
        $this->assertTrue($resp1->headers->has('Link'));
        $this->assertStringContainsString('rel="next"', $resp1->headers->get('Link'));
        $this->assertStringNotContainsString('rel="prev"', $resp1->headers->get('Link'));
        // Second page: offset 30, expect both prev and next
        $resp2 = $this->actingAs($user)
            ->getJson(route('emc.core.saved-views.index', ['limit' => 30, 'offset' => 30]));
        $resp2->assertOk()->assertJsonCount(30);
        $this->assertTrue($resp2->headers->has('Link'));
        $this->assertStringContainsString('rel="prev"', $resp2->headers->get('Link'));
        $this->assertStringContainsString('rel="next"', $resp2->headers->get('Link'));
        // Last page: offset 60, expect only prev
        $resp3 = $this->actingAs($user)
            ->getJson(route('emc.core.saved-views.index', ['limit' => 30, 'offset' => 60]));
        $resp3->assertOk()->assertJsonCount(15);
        $this->assertTrue($resp3->headers->has('Link'));
        $this->assertStringContainsString('rel="prev"', $resp3->headers->get('Link'));
        $this->assertStringNotContainsString('rel="next"', $resp3->headers->get('Link'));
    }

    public function test_update_and_duplicate_endpoints(): void
    {
        /** @var User&\Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create();
        // Create base view
        $create = $this->actingAs($user)
            ->postJson(route('emc.core.saved-views.store'), [
                'name' => 'Base',
                'context' => 'core_databases',
                'filters' => ['engine' => 'PostgreSQL', 'env' => 'Prod'],
            ])->assertCreated()->json();

        // Rename via PATCH
        $this->actingAs($user)
            ->patchJson(route('emc.core.saved-views.update', $create['id']), [
                'name' => 'Renamed',
            ])->assertOk()->assertJsonFragment(['name' => 'Renamed']);

        // Duplicate with new name
        $dup = $this->actingAs($user)
            ->postJson(route('emc.core.saved-views.duplicate', $create['id']), [
                'name' => 'Renamed (copy)',
            ])->assertCreated()->json();
        $this->assertArrayHasKey('id', $dup);
        $this->assertSame(2, SavedView::query()->where('user_id', $user->id)->count());

        // Search should find both when q='Renamed'
        $resp = $this->actingAs($user)
            ->getJson(route('emc.core.saved-views.index', ['q' => 'Renamed', 'limit' => 50]));
        $resp->assertOk();
        $names = array_column($resp->json(), 'name');
        $this->assertContains('Renamed', $names);
        $this->assertContains('Renamed (copy)', $names);
    }

    public function test_rename_conflict_and_duplicate_conflict_returns_422(): void
    {
        /** @var User&\Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create();
        // Create two views
        $first = $this->actingAs($user)
            ->postJson(route('emc.core.saved-views.store'), [
                'name' => 'A',
                'filters' => ['env' => 'Prod'],
            ])->assertCreated()->json();
        $second = $this->actingAs($user)
            ->postJson(route('emc.core.saved-views.store'), [
                'name' => 'B',
                'filters' => ['env' => 'Dev'],
            ])->assertCreated()->json();

        // Renaming B to A should 422
        $this->actingAs($user)
            ->patchJson(route('emc.core.saved-views.update', $second['id']), [
                'name' => 'A',
            ])->assertStatus(422);

        // Duplicating A as A should 422
        $this->actingAs($user)
            ->postJson(route('emc.core.saved-views.duplicate', $first['id']), [
                'name' => 'A',
            ])->assertStatus(422);
    }

    public function test_limit_cap_from_config_is_applied(): void
    {
        /** @var User&\Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create();
        // Set config to a low cap
        config()->set('emc.saved_views.limit_cap', 30);
        config()->set('emc.saved_views.default_limit', 7);
        // Seed 100 views
        SavedView::factory()->count(100)->create(['user_id' => $user->id]);
        // Request an overly large limit; expect cap 30
        $resp = $this->actingAs($user)
            ->getJson(route('emc.core.saved-views.index', ['limit' => 999]));
        $resp->assertOk()->assertJsonCount(30);
        $resp->assertHeader('X-SavedViews-Limit', '30');

        // Negative limit uses default_limit (7)
        $resp2 = $this->actingAs($user)
            ->getJson(route('emc.core.saved-views.index', ['limit' => -5]));
        $resp2->assertOk()->assertJsonCount(7);
        $resp2->assertHeader('X-SavedViews-Limit', '7');
    }
}
