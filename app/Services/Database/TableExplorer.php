<?php

declare(strict_types=1);

namespace App\Services\Database;

use App\Models\DatabaseConnection;

final class TableExplorer
{
    public function __construct(private DynamicConnectorInterface $connector) {}

    private static function toString(mixed $v): string
    {
        return is_scalar($v) ? (string) $v : '';
    }

    /** @return list<string> */
    public function listTables(DatabaseConnection $conn): array
    {
        $db = $this->connector->connect($conn);
        $driver = (string) $conn->driver;
        $database = (string) $conn->database;

        switch ($driver) {
            case 'mysql':
                /** @var array<int, array<string, mixed>> $rows */
                $rows = array_map(static fn ($r) => (array) $r, $db->select('SELECT TABLE_NAME AS table_name FROM information_schema.tables WHERE table_schema = ? ORDER BY 1', [$database]));

                return array_values(array_map(static fn (array $row): string => self::toString($row['table_name'] ?? null), $rows));
            case 'pgsql':
                /** @var array<int, array<string, mixed>> $rows */
                $rows = array_map(static fn ($r) => (array) $r, $db->select("SELECT table_name FROM information_schema.tables WHERE table_schema IN ('public') ORDER BY 1"));

                return array_values(array_map(static fn (array $row): string => self::toString($row['table_name'] ?? null), $rows));
            case 'sqlsrv':
                /** @var array<int, array<string, mixed>> $rows */
                $rows = array_map(static fn ($r) => (array) $r, $db->select("SELECT TABLE_NAME AS table_name FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE='BASE TABLE' ORDER BY 1"));

                return array_values(array_map(static fn (array $row): string => self::toString($row['table_name'] ?? $row['TABLE_NAME'] ?? null), $rows));
            case 'sqlite':
                /** @var array<int, array<string, mixed>> $rows */
                $rows = array_map(static fn ($r) => (array) $r, $db->select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY 1"));

                return array_values(array_map(static fn (array $row): string => self::toString($row['name'] ?? null), $rows));
            default:
                return [];
        }
    }

    /** @return array<int, array{column:string,type:string,is_nullable:bool,default:mixed}> */
    public function listColumns(DatabaseConnection $conn, string $table): array
    {
        $db = $this->connector->connect($conn);
        $driver = (string) $conn->driver;
        $database = (string) $conn->database;
        $table = (string) $table;

        switch ($driver) {
            case 'mysql':
                /** @var array<int, array<string, mixed>> $rows */
                $rows = array_map(static fn ($r) => (array) $r, $db->select('SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT FROM information_schema.columns WHERE table_schema = ? AND table_name = ? ORDER BY ORDINAL_POSITION', [$database, $table]));

                return array_values(array_map(static fn (array $row) => ['column' => self::toString($row['COLUMN_NAME'] ?? null), 'type' => self::toString($row['DATA_TYPE'] ?? null), 'is_nullable' => (self::toString($row['IS_NULLABLE'] ?? null) === 'YES'), 'default' => $row['COLUMN_DEFAULT'] ?? null], $rows));
            case 'pgsql':
                /** @var array<int, array<string, mixed>> $rows */
                $rows = array_map(static fn ($r) => (array) $r, $db->select("SELECT column_name, data_type, is_nullable, column_default FROM information_schema.columns WHERE table_schema='public' AND table_name = ? ORDER BY ordinal_position", [$table]));

                return array_values(array_map(static fn (array $row) => ['column' => self::toString($row['column_name'] ?? null), 'type' => self::toString($row['data_type'] ?? null), 'is_nullable' => (self::toString($row['is_nullable'] ?? null) === 'YES'), 'default' => $row['column_default'] ?? null], $rows));
            case 'sqlsrv':
                /** @var array<int, array<string, mixed>> $rows */
                $rows = array_map(static fn ($r) => (array) $r, $db->select('SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ? ORDER BY ORDINAL_POSITION', [$table]));

                return array_values(array_map(static fn (array $row) => ['column' => self::toString($row['COLUMN_NAME'] ?? null), 'type' => self::toString($row['DATA_TYPE'] ?? null), 'is_nullable' => (self::toString($row['IS_NULLABLE'] ?? null) === 'YES'), 'default' => $row['COLUMN_DEFAULT'] ?? null], $rows));
            case 'sqlite':
                /** @var array<int, array<string, mixed>> $rows */
                $rows = array_map(static fn ($r) => (array) $r, $db->select("PRAGMA table_info('".str_replace("'", "''", $table)."')"));

                return array_values(array_map(static fn (array $row) => ['column' => self::toString($row['name'] ?? null), 'type' => self::toString($row['type'] ?? null), 'is_nullable' => ($row['notnull'] ?? 0) == 0, 'default' => $row['dflt_value'] ?? null], $rows));
            default:
                return [];
        }
    }

    /** @return array<int, array{table:string,column:string,ref_table:string,ref_column:string}> */
    public function listForeignKeys(DatabaseConnection $conn, string $table): array
    {
        $db = $this->connector->connect($conn);

        $driver = (string) $conn->driver;
        $database = (string) $conn->database;
        $table = (string) $table;

        switch ($driver) {
            case 'mysql':
                /** @var array<int, array<string, mixed>> $rows */
                $rows = array_map(static fn ($r) => (array) $r, $db->select('SELECT kcu.TABLE_NAME, kcu.COLUMN_NAME, kcu.REFERENCED_TABLE_NAME, kcu.REFERENCED_COLUMN_NAME FROM information_schema.key_column_usage kcu WHERE kcu.TABLE_SCHEMA = ? AND kcu.TABLE_NAME = ? AND kcu.REFERENCED_TABLE_NAME IS NOT NULL', [$database, $table]));

                return array_values(array_map(static fn (array $row) => ['table' => self::toString($row['TABLE_NAME'] ?? null), 'column' => self::toString($row['COLUMN_NAME'] ?? null), 'ref_table' => self::toString($row['REFERENCED_TABLE_NAME'] ?? null), 'ref_column' => self::toString($row['REFERENCED_COLUMN_NAME'] ?? null)], $rows));
            case 'pgsql':
                /** @var array<int, array<string, mixed>> $rows */
                $rows = array_map(static fn ($r) => (array) $r, $db->select("SELECT tc.table_name, kcu.column_name, ccu.table_name AS foreign_table_name, ccu.column_name AS foreign_column_name FROM information_schema.table_constraints AS tc JOIN information_schema.key_column_usage AS kcu ON tc.constraint_name = kcu.constraint_name JOIN information_schema.constraint_column_usage AS ccu ON ccu.constraint_name = tc.constraint_name WHERE tc.constraint_type = 'FOREIGN KEY' AND tc.table_name = ?", [$table]));

                return array_values(array_map(static fn (array $row) => ['table' => self::toString($row['table_name'] ?? null), 'column' => self::toString($row['column_name'] ?? null), 'ref_table' => self::toString($row['foreign_table_name'] ?? null), 'ref_column' => self::toString($row['foreign_column_name'] ?? null)], $rows));
            case 'sqlsrv':
                /** @var array<int, array<string, mixed>> $rows */
                $rows = array_map(static fn ($r) => (array) $r, $db->select("SELECT FK.TABLE_NAME AS FK_TABLE, CU.COLUMN_NAME AS FK_COLUMN, PK.TABLE_NAME AS PK_TABLE, PT.COLUMN_NAME AS PK_COLUMN FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS C INNER JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS FK ON C.CONSTRAINT_NAME = FK.CONSTRAINT_NAME INNER JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS PK ON C.UNIQUE_CONSTRAINT_NAME = PK.CONSTRAINT_NAME INNER JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE CU ON C.CONSTRAINT_NAME = CU.CONSTRAINT_NAME INNER JOIN ( SELECT i1.TABLE_NAME, i2.COLUMN_NAME FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS i1 INNER JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE i2 ON i1.CONSTRAINT_NAME = i2.CONSTRAINT_NAME WHERE i1.CONSTRAINT_TYPE = 'PRIMARY KEY' ) PT ON PT.TABLE_NAME = PK.TABLE_NAME WHERE FK.TABLE_NAME = ?", [$table]));

                return array_values(array_map(static fn (array $row) => ['table' => self::toString($row['FK_TABLE'] ?? null), 'column' => self::toString($row['FK_COLUMN'] ?? null), 'ref_table' => self::toString($row['PK_TABLE'] ?? null), 'ref_column' => self::toString($row['PK_COLUMN'] ?? null)], $rows));
            case 'sqlite':
                /** @var array<int, array<string, mixed>> $rows */
                $rows = array_map(static fn ($r) => (array) $r, $db->select("PRAGMA foreign_key_list('".str_replace("'", "''", $table)."')"));

                return array_values(array_map(static fn (array $row) => ['table' => $table, 'column' => self::toString($row['from'] ?? null), 'ref_table' => self::toString($row['table'] ?? null), 'ref_column' => self::toString($row['to'] ?? null)], $rows));
            default:
                return [];
        }
    }
}
