<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('core_database_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('core_database_id')->constrained('core_databases')->cascadeOnDelete();
            $table->foreignId('database_connection_id')->nullable()->constrained('database_connections')->nullOnDelete();
            $table->string('linked_connection_name');
            $table->string('link_type');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('core_database_links');
    }
};
