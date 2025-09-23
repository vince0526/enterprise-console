<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateDatabaseConnectionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create database_connections table
        Schema::create('database_connections', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('driver'); // mysql, pgsql, sqlsrv, sqlite
            $table->string('host')->nullable();
            $table->unsignedInteger('port')->nullable();
            $table->string('database')->nullable();
            $table->string('username')->nullable();
            $table->text('password')->nullable(); // store encrypted (via service)
            $table->json('options')->nullable();  // ssl certs, timeouts, etc
            $table->string('status')->default('inactive'); // inactive|active|error
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('database_connections');
    }
}
