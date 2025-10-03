<?php

declare(strict_types=1);

use App\Models\CoreDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

it('returns DDL for Postgres and MySQL', function () {
    // Postgres
    $resPg = post(route('emc.core.ddl'), [
        'engine' => 'PostgreSQL',
        'functional_scopes' => ['Accounting', 'Inventory'],
    ]);
    $resPg->assertOk();
    expect($resPg->getContent())
        ->toContain('CREATE SCHEMA IF NOT EXISTS acc_')
        ->toContain('CREATE TABLE IF NOT EXISTS acc_.ledger')
        ->toContain('CREATE TABLE IF NOT EXISTS inv_.item');

    // MySQL
    $resMy = post(route('emc.core.ddl'), [
        'engine' => 'MySQL',
        'functional_scopes' => ['HRM'],
    ]);
    $resMy->assertOk();
    expect($resMy->getContent())
        ->toContain('-- MySQL: table names will be prefixed with acc_')
        ->toContain('CREATE TABLE IF NOT EXISTS hr_person');
});

it('filters registry by engine via query string', function () {
    CoreDatabase::factory()->create(['name' => 'pg_db', 'engine' => 'PostgreSQL', 'env' => 'Dev']);
    CoreDatabase::factory()->create(['name' => 'my_db', 'engine' => 'MySQL', 'env' => 'Dev']);

    $res = get(route('emc.core.index', ['engine' => 'MySQL']));
    $res->assertOk();
    $html = $res->getContent();
    expect($html)->toContain('my_db');
    expect($html)->not->toContain('pg_db');
});
