<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('company_user_restrictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('database_connection_id')->constrained()->cascadeOnDelete();
            $table->json('allowed_tables')->nullable();    // ["customers","orders"]
            $table->json('allowed_processes')->nullable(); // ["import","create_table"]
            $table->timestamps();
            $table->unique(['user_id', 'database_connection_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_user_restrictions');
    }
};
