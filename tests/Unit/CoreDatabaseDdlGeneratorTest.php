<?php

declare(strict_types=1);

use App\Services\CoreDatabaseDdlGenerator;

it('generates postgres ddl with selected scopes', function () {
    $svc = new CoreDatabaseDdlGenerator;
    $sql = $svc->generate('PostgreSQL', ['Accounting', 'Inventory']);
    expect($sql)->toContain('CREATE SCHEMA IF NOT EXISTS acc_')
        ->and($sql)->toContain('CREATE SCHEMA IF NOT EXISTS inv_')
        ->and($sql)->toContain('acc_.ledger')
        ->and($sql)->toContain('inv_.item');
});

it('generates mysql ddl with selected scopes', function () {
    $svc = new CoreDatabaseDdlGenerator;
    $sql = $svc->generate('MySQL', ['Compliance']);
    // Should contain schema prefix comment and a CREATE TABLE for compliance table
    expect($sql)->toContain('table names will be prefixed with acc_');
    expect($sql)->toContain('CREATE TABLE IF NOT EXISTS gov_obligation_evidence');
});
