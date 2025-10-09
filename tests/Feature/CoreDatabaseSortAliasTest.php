<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\CoreDatabase;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoreDatabaseSortAliasTest extends TestCase
{
    use RefreshDatabase;

    public function test_sort_alias_environment_maps_to_env(): void
    {
        /** @var User&AuthenticatableContract $user */
        $user = User::factory()->create();
        CoreDatabase::factory()->create(['name' => 'db_a', 'engine' => 'PostgreSQL', 'env' => 'Dev']);
        CoreDatabase::factory()->create(['name' => 'db_b', 'engine' => 'PostgreSQL', 'env' => 'Prod']);

        $resp = $this->actingAs($user)->get(route('emc.core.index', [
            'sortBy' => 'environment',
            'sortDir' => 'desc',
        ]));
        $resp->assertStatus(200);
        // Ensure response contains table with env values, and ordering places Prod before Dev.
        $html = $resp->getContent();
        $prodPos = strpos($html, 'Prod');
        $devPos = strpos($html, 'Dev');
        $this->assertIsInt($prodPos);
        $this->assertIsInt($devPos);
        $this->assertTrue($prodPos < $devPos, 'Expected Prod to appear before Dev when sorting by environment desc');
    }

    public function test_sort_alias_platform_maps_to_engine(): void
    {
        /** @var User&AuthenticatableContract $user */
        $user = User::factory()->create();
        CoreDatabase::factory()->create(['name' => 'db_mysql', 'engine' => 'MySQL', 'env' => 'Dev']);
        CoreDatabase::factory()->create(['name' => 'db_pg', 'engine' => 'PostgreSQL', 'env' => 'Dev']);

        $resp = $this->actingAs($user)->get(route('emc.core.index', [
            'sortBy' => 'platform',
            'sortDir' => 'asc',
        ]));
        $resp->assertStatus(200);
        $html = $resp->getContent();
        $mysqlPos = strpos($html, 'MySQL');
        $pgPos = strpos($html, 'PostgreSQL');
        $this->assertIsInt($mysqlPos);
        $this->assertIsInt($pgPos);
        $this->assertTrue($mysqlPos < $pgPos, 'Expected MySQL to appear before PostgreSQL when sorting by platform asc');
    }
}
