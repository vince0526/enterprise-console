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
        $conn = new DatabaseConnection;
        $conn->driver = 'sqlite';
        $conn->database = ':memory:';

        $dbStub = new class
        {
            public function select(string $sql)
            {
                return [(object) ['name' => 'users'], (object) ['name' => 'posts']];
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
        $conn = new DatabaseConnection;
        $conn->driver = 'sqlite';
        $conn->database = ':memory:';

        $dbStub = new class
        {
            public function select(string $sql)
            {
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
        $conn = new DatabaseConnection;
        $conn->driver = 'sqlite';
        $conn->database = ':memory:';

        $dbStub = new class
        {
            public function select(string $sql)
            {
                return [(object) ['id' => 0, 'seq' => 0, 'table' => 'companies', 'from' => 'company_id', 'to' => 'id']];
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
