<?php

declare(strict_types=1);

namespace App\Services;

/**
 * CoreDatabaseDdlGenerator
 *
 * Purpose: Encapsulates engine-specific SQL DDL generation for the Core Databases module
 * based on selected functional scopes.
 *
 * How to extend:
 * - To support a new engine, add cases to $createSchema and $createTable match() blocks.
 * - To add new functional scopes, add new if (isset($wants['YourScope'])) blocks mapping to schema/table pairs.
 * - For production-grade DDL (PK/FK/indexes), expand the table definitions and split into multiple statements.
 */
final class CoreDatabaseDdlGenerator
{
    /**
     * @param  array<string>  $functionalScopes
     */
    public function generate(?string $engine, array $functionalScopes): string
    {
        $engine = $engine ?: 'PostgreSQL';
        $schemas = ['acc_', 'inv_', 'mfg_', 'hr_', 'log_', 'gov_', 'media_', 'fin_', 'kpi_', 'ref_'];
        $out = [];

        $createSchema = static function (string $e, string $s): string {
            return match ($e) {
                'PostgreSQL' => "CREATE SCHEMA IF NOT EXISTS {$s};",
                'MySQL' => "-- MySQL: table names will be prefixed with {$s}",
                'SQL Server' => "IF NOT EXISTS (SELECT 1 FROM sys.schemas WHERE name = '{$s}') EXEC('CREATE SCHEMA {$s}');",
                default => '-- Unknown engine',
            };
        };

        $createTable = static function (string $e, string $schema, string $name): string {
            return match ($e) {
                'PostgreSQL' => "CREATE TABLE IF NOT EXISTS {$schema}.{$name} (id BIGSERIAL PRIMARY KEY, created_at TIMESTAMPTZ DEFAULT now());",
                'MySQL' => "CREATE TABLE IF NOT EXISTS {$schema}{$name} (id BIGINT AUTO_INCREMENT PRIMARY KEY, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB;",
                'SQL Server' => "IF OBJECT_ID('{$schema}.{$name}', 'U') IS NULL CREATE TABLE {$schema}.{$name} (id BIGINT IDENTITY(1,1) PRIMARY KEY, created_at DATETIME2 DEFAULT SYSUTCDATETIME());",
                default => '-- Unknown engine',
            };
        };

        // Ensure all known schemas (or name prefixes) are created for the chosen engine.
        foreach ($schemas as $s) {
            $out[] = $createSchema($engine, $s);
        }

        $wants = array_flip($functionalScopes);
        // Map functional scopes to representative tables per schema/prefix.
        if (isset($wants['Accounting'])) {
            $out[] = $createTable($engine, 'acc_', 'ledger');
        }
        if (isset($wants['Inventory'])) {
            $out[] = $createTable($engine, 'inv_', 'item');
        }
        if (isset($wants['Manufacturing'])) {
            $out[] = $createTable($engine, 'mfg_', 'work_order');
        }
        if (isset($wants['HRM'])) {
            $out[] = $createTable($engine, 'hr_', 'person');
        }
        if (isset($wants['Logistics'])) {
            $out[] = $createTable($engine, 'log_', 'shipment');
        }
        if (isset($wants['Compliance'])) {
            $out[] = $createTable($engine, 'gov_', 'obligation_evidence');
        }
        if (isset($wants['MediaSpecific'])) {
            $out[] = $createTable($engine, 'media_', 'platform_kpi');
        }
        if (isset($wants['FinanceSpecific'])) {
            $out[] = $createTable($engine, 'fin_', 'institution_license');
        }

        return implode("\n", $out) . "\n";
    }
}
