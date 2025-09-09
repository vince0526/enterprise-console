<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\DatabaseConnection;
use App\Services\Database\DynamicConnectorInterface;
use App\Services\Database\TableExplorer;
use PHPUnit\Framework\TestCase;

final class TableExplorerTest extends TestCase
{
    public function test_list_tables_for_sqlite(): void
    {
        $conn = new DatabaseConnection();
        $conn->driver = 'sqlite';
        $conn->database = ':memory:';

        // Stub DB connection that returns rows for the select() call
        $dbStub = new class {
            public function select(string $sql)
            {
                return [ (object) ['name' => 'users'], (object) ['name' => 'posts'] ];
            }
        };

        $connector = $this->createMock(DynamicConnectorInterface::class);
        $connector->expects($this->once())->method('connect')->with($conn)->willReturn($dbStub);

        $explorer = new TableExplorer($connector);
        $tables = $explorer->listTables($conn);

        $this->assertIsArray($tables);
        $this->assertContains('users', $tables);
        $this->assertContains('posts', $tables);
    }

    public function test_list_columns_for_sqlite(): void
    {
        $conn = new DatabaseConnection();
        $conn->driver = 'sqlite';
        $conn->database = ':memory:';

        $dbStub = new class {
            public function select(string $sql)
            {
                // simulate PRAGMA table_info output
                return [
                    (object) ['cid' => 0, 'name' => 'id', 'type' => 'integer', 'notnull' => 1, 'dflt_value' => null],
                    (object) ['cid' => 1, 'name' => 'name', 'type' => 'text', 'notnull' => 0, 'dflt_value' => null],
                ];
            }
        };

        $connector = $this->createMock(DynamicConnectorInterface::class);
        $connector->method('connect')->willReturn($dbStub);

        $explorer = new TableExplorer($connector);
        $cols = $explorer->listColumns($conn, 'users');

        $this->assertCount(2, $cols);
        $this->assertEquals('id', $cols[0]['column']);
        $this->assertEquals('integer', $cols[0]['type']);
        $this->assertFalse($cols[0]['is_nullable']);
        $this->assertEquals('name', $cols[1]['column']);
    }

    public function test_list_foreign_keys_for_sqlite(): void
    {
        $conn = new DatabaseConnection();
        $conn->driver = 'sqlite';
        $conn->database = ':memory:';

        $dbStub = new class {
            public function select(string $sql)
            {
                // simulate PRAGMA foreign_key_list output
                return [ (object) ['id' => 0, 'seq' => 0, 'table' => 'companies', 'from' => 'company_id', 'to' => 'id'] ];
            }
        };

        $connector = $this->createMock(DynamicConnectorInterface::class);
        $connector->method('connect')->willReturn($dbStub);

        $explorer = new TableExplorer($connector);
        $fks = $explorer->listForeignKeys($conn, 'users');

        $this->assertCount(1, $fks);
        $this->assertEquals('users', $fks[0]['table']);
        $this->assertEquals('company_id', $fks[0]['column']);
        $this->assertEquals('companies', $fks[0]['ref_table']);
        $this->assertEquals('id', $fks[0]['ref_column']);
    }
}
<?php

declare(strict_types=1);
}
            {
                // simulate PRAGMA foreign_key_list output
                return [ (object) ['id' => 0, 'seq' => 0, 'table' => 'companies', 'from' => 'company_id', 'to' => 'id'] ];
            }
        };

        $connector = $this->createMock(DynamicConnectorInterface::class);
        $connector->method('connect')->willReturn($dbStub);

        $explorer = new TableExplorer($connector);
        $fks = $explorer->listForeignKeys($conn, 'users');

        $this->assertCount(1, $fks);
        $this->assertEquals('users', $fks[0]['table']);
        $this->assertEquals('company_id', $fks[0]['column']);
        $this->assertEquals('companies', $fks[0]['ref_table']);
        $this->assertEquals('id', $fks[0]['ref_column']);
    }
}
<?php

declare(strict_types=1);

use App\Models\DatabaseConnection;
use App\Services\Database\TableExplorer;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mockery;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('can list tables, columns and foreign keys for sqlite', function () {
    // Ensure the in-memory sqlite connection exists
    $conn = DB::connection();

    // Create a sample table with a foreign key
    Schema::create('parents', function (Blueprint $table) {
        $table->id();
        $table->string('name');
    });

    Schema::create('children', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('parent_id');
        $table->string('name');
        $table->foreign('parent_id')->references('id')->on('parents');
    });

    // Mock DatabaseConnection model
    $dbConn = new \App\Models\DatabaseConnection;
    $dbConn->driver = 'sqlite';
    $dbConn->database = ':memory:';

    // Instead of mocking the DB facade, mock the DynamicConnector so it returns
    // the current in-memory connection when asked. This avoids runtime
    // configuration changes and migration ordering issues.
    // Create a small anonymous stub that provides the same connect signature
    // as the real DynamicConnector. This avoids Mockery's limitation with
    // final classes and keeps the unit test isolated from runtime
    // connection registration.
    $stubConnector = new class implements \App\Services\Database\DynamicConnectorInterface
    {
        public function connect(\App\Models\DatabaseConnection $c)
        {
            // Return the current test connection instance
            return \Illuminate\Support\Facades\DB::connection();
        }
    };

    $explorer = new TableExplorer($stubConnector);

    $tables = $explorer->listTables($dbConn);
    expect($tables)->toContain('parents')->toContain('children');

    $cols = $explorer->listColumns($dbConn, 'children');
    expect(array_column($cols, 'column'))->toContain('id')->toContain('parent_id')->toContain('name');

    $fks = $explorer->listForeignKeys($dbConn, 'children');
    expect($fks)->not->toBeEmpty();

    // Clean up
    Schema::dropIfExists('children');
    Schema::dropIfExists('parents');

    // no Mockery used
});
