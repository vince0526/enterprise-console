<?php

declare(strict_types=1);

use App\Services\Database\TableExplorer;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Uses binding is declared in tests/Pest.php

it('can list tables, columns and foreign keys for sqlite', function () {
    // Ensure the in-memory sqlite connection exists
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

    $stubConnector = new class implements \App\Services\Database\DynamicConnectorInterface
    {
        public function connect(\App\Models\DatabaseConnection $c)
        {
            // Return the current test connection instance
            return DB::connection();
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
});
