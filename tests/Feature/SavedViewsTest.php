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
        $this->actingAs($user)
            ->getJson(route('emc.core.saved-views.index'))
            ->assertOk()
            ->assertExactJson([]);

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
        $this->actingAs($user)
            ->getJson(route('emc.core.saved-views.index'))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['name' => 'My Prod PG']);

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
}
