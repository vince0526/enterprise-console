<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('core_databases', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('environment'); // Production|Staging|Development
            $table->string('platform');    // MySQL|PostgreSQL|SQL Server|MongoDB
            $table->string('owner')->nullable();
            $table->string('lifecycle')->nullable(); // Long-lived|Temporary|Archived
            $table->string('linked_connection')->nullable(); // database_connections.name
            $table->text('description')->nullable();
            $table->string('status')->default('healthy');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('core_databases');
    }
};
