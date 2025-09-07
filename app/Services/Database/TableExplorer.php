<?php

declare(strict_types=1);

namespace App\Services\Database;

use App\Models\DatabaseConnection;

final class TableExplorer
{
    public function __construct(private DynamicConnector $connector) {}

    /** @return list<string> */
    public function listTables(DatabaseConnection $conn): array
    {
        $db = $this->connector->connect($conn);

        return match ($conn->driver) {
            'mysql' => array_map(
                static fn ($r) => (string) $r->table_name,
                $db->select('SELECT TABLE_NAME AS table_name FROM information_schema.tables WHERE table_schema = ? ORDER BY 1', [$conn->database])
            ),
            'pgsql' => array_map(
                static fn ($r) => (string) $r->table_name,
                $db->select("SELECT table_name FROM information_schema.tables WHERE table_schema IN ('public') ORDER BY 1")
            ),
            'sqlsrv' => array_map(
                static fn ($r) => (string) $r->table_name,
                $db->select("SELECT TABLE_NAME AS table_name FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE='BASE TABLE' ORDER BY 1")
            ),
            'sqlite' => array_map(
                static fn ($r) => (string) $r->name,
                $db->select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY 1")
            ),
            default => [],
        };
    }

    /** @return array<int, array{column:string,type:string,is_nullable:bool,default:mixed}> */
    public function listColumns(DatabaseConnection $conn, string $table): array
    {
        $db = $this->connector->connect($conn);

        return match ($conn->driver) {
            'mysql' => array_map(static fn ($r) => [
                'column' => (string) $r->COLUMN_NAME,
                'type' => (string) $r->DATA_TYPE,
                'is_nullable' => ((string) $r->IS_NULLABLE) === 'YES',
                'default' => $r->COLUMN_DEFAULT,
            ], $db->select(
                'SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT
                 FROM information_schema.columns
                 WHERE table_schema = ? AND table_name = ?
                 ORDER BY ORDINAL_POSITION', [$conn->database, $table]
            )),
            'pgsql' => array_map(static fn ($r) => [
                'column' => (string) $r->column_name,
                'type' => (string) $r->data_type,
                'is_nullable' => ((string) $r->is_nullable) === 'YES',
                'default' => $r->column_default,
            ], $db->select(
                "SELECT column_name, data_type, is_nullable, column_default
                 FROM information_schema.columns
                 WHERE table_schema='public' AND table_name = ?
                 ORDER BY ordinal_position", [$table]
            )),
            'sqlsrv' => array_map(static fn ($r) => [
                'column' => (string) $r->COLUMN_NAME,
                'type' => (string) $r->DATA_TYPE,
                'is_nullable' => ((string) $r->IS_NULLABLE) === 'YES',
                'default' => $r->COLUMN_DEFAULT,
            ], $db->select(
                'SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT
                 FROM INFORMATION_SCHEMA.COLUMNS
                 WHERE TABLE_NAME = ?
                 ORDER BY ORDINAL_POSITION', [$table]
            )),
            'sqlite' => array_map(static fn ($r) => [
                'column' => (string) $r->name,
                'type' => (string) $r->type,
                'is_nullable' => $r->notnull == 0,
                'default' => $r->dflt_value,
            ], $db->select("PRAGMA table_info('$table')")),
            default => [],
        };
    }

    /** @return array<int, array{table:string,column:string,ref_table:string,ref_column:string}> */
    public function listForeignKeys(DatabaseConnection $conn, string $table): array
    {
        $db = $this->connector->connect($conn);

        return match ($conn->driver) {
            'mysql' => array_map(static fn ($r) => [
                'table' => (string) $r->TABLE_NAME,
                'column' => (string) $r->COLUMN_NAME,
                'ref_table' => (string) $r->REFERENCED_TABLE_NAME,
                'ref_column' => (string) $r->REFERENCED_COLUMN_NAME,
            ], $db->select(
                'SELECT kcu.TABLE_NAME, kcu.COLUMN_NAME, kcu.REFERENCED_TABLE_NAME, kcu.REFERENCED_COLUMN_NAME
                 FROM information_schema.key_column_usage kcu
                 WHERE kcu.TABLE_SCHEMA = ? AND kcu.TABLE_NAME = ? AND kcu.REFERENCED_TABLE_NAME IS NOT NULL',
                [$conn->database, $table]
            )),
            'pgsql' => array_map(static fn ($r) => [
                'table' => (string) $r->table_name,
                'column' => (string) $r->column_name,
                'ref_table' => (string) $r->foreign_table_name,
                'ref_column' => (string) $r->foreign_column_name,
            ], $db->select(
                "SELECT
                   tc.table_name, kcu.column_name,
                   ccu.table_name AS foreign_table_name,
                   ccu.column_name AS foreign_column_name
                 FROM information_schema.table_constraints AS tc
                 JOIN information_schema.key_column_usage AS kcu
                   ON tc.constraint_name = kcu.constraint_name
                 JOIN information_schema.constraint_column_usage AS ccu
                   ON ccu.constraint_name = tc.constraint_name
                 WHERE tc.constraint_type = 'FOREIGN KEY' AND tc.table_name = ?",
                [$table]
            )),
            'sqlsrv' => array_map(static fn ($r) => [
                'table' => (string) $r->FK_TABLE,
                'column' => (string) $r->FK_COLUMN,
                'ref_table' => (string) $r->PK_TABLE,
                'ref_column' => (string) $r->PK_COLUMN,
            ], $db->select(
                "SELECT
                   FK.TABLE_NAME AS FK_TABLE, CU.COLUMN_NAME AS FK_COLUMN,
                   PK.TABLE_NAME AS PK_TABLE, PT.COLUMN_NAME AS PK_COLUMN
                 FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS C
                 INNER JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS FK
                   ON C.CONSTRAINT_NAME = FK.CONSTRAINT_NAME
                 INNER JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS PK
                   ON C.UNIQUE_CONSTRAINT_NAME = PK.CONSTRAINT_NAME
                 INNER JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE CU
                   ON C.CONSTRAINT_NAME = CU.CONSTRAINT_NAME
                 INNER JOIN (
                   SELECT i1.TABLE_NAME, i2.COLUMN_NAME
                   FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS i1
                   INNER JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE i2
                   ON i1.CONSTRAINT_NAME = i2.CONSTRAINT_NAME
                   WHERE i1.CONSTRAINT_TYPE = 'PRIMARY KEY'
                 ) PT ON PT.TABLE_NAME = PK.TABLE_NAME
                 WHERE FK.TABLE_NAME = ?", [$table]
            )),
            'sqlite' => array_map(static fn ($r) => [
                'table' => $table,
                'column' => (string) $r->from,
                'ref_table' => (string) $r->table,
                'ref_column' => (string) $r->to,
            ], $db->select("PRAGMA foreign_key_list('$table')")),
            default => [],
        };
    }
}
