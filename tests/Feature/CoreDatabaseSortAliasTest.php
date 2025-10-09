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

        // The full page contains wizard select options listing engines (PostgreSQL, MySQL, ...)
        // before the registry table, which can cause substring position assertions to produce
        // false negatives. Limit our search scope to the registry table container so ordering
        // reflects the actual sorted result set.
        $tableStart = strpos($html, 'id="coreDbsTable"');
        $scoped = $tableStart !== false ? substr($html, $tableStart) : $html;
        // Further narrow to tbody content to avoid filter selects containing engine names.
        $tbodyStart = strpos($scoped, '<tbody');
        if ($tbodyStart !== false) {
            $tbodyFragment = substr($scoped, $tbodyStart);
            $tbodyEnd = strpos($tbodyFragment, '</tbody>');
            if ($tbodyEnd !== false) {
                $scoped = substr($tbodyFragment, 0, $tbodyEnd);
            } else {
                $scoped = $tbodyFragment;
            }
        }

        // Use row name markers to reduce risk of picking up engine names from other columns
        $mysqlPos = strpos($scoped, 'db_mysql');
        $pgPos = strpos($scoped, 'db_pg');
        $this->assertIsInt($mysqlPos);
        $this->assertIsInt($pgPos);
        $this->assertTrue($mysqlPos < $pgPos, 'Expected db_mysql row to appear before db_pg when sorting by platform asc within registry table');
    }
}
