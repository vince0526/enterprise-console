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
        Schema::create('grid_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('table_key');           // e.g. db:1.table:customers
            $table->json('columns')->nullable();   // visibility/order/width
            $table->json('filters')->nullable();   // excel-like filters
            $table->json('sorts')->nullable();
            $table->json('formats')->nullable();   // number/text formats
            $table->timestamps();
            $table->unique(['user_id', 'table_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grid_settings');
    }
};
