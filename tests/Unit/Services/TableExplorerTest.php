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
