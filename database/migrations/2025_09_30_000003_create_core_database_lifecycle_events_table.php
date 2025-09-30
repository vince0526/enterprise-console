<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('core_database_lifecycle_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('core_database_id')->constrained('core_databases')->cascadeOnDelete();
            $table->string('event_type'); // e.g., Created, Decommissioned, Backup
            $table->text('details')->nullable();
            $table->date('effective_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('core_database_lifecycle_events');
    }
};
