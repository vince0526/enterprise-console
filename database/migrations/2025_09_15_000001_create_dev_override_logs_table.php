<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dev_override_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('email');
            $table->string('ip', 45)->nullable();
            $table->timestamps();
            $table->index(['user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dev_override_logs');
    }
};
