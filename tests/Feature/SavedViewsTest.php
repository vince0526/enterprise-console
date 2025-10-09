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
}
