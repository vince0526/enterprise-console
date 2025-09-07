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
        Schema::create('ftp_connections', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->enum('protocol', ['ftp', 'sftp'])->default('ftp');
            $table->string('host');
            $table->unsignedInteger('port')->nullable();
            $table->string('username')->nullable();
            $table->text('password')->nullable(); // encrypt via service
            $table->string('root_path')->nullable();
            $table->boolean('passive')->default(true);
            $table->boolean('ssl')->default(false);
            $table->json('options')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ftp_connections');
    }
};
