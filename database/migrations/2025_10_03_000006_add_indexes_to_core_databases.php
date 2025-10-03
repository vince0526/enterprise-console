<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('core_databases', function (Blueprint $table) {
            // Add indexes to speed up registry filtering and sorting
            $table->index('tier', 'core_db_tier_idx');
            $table->index('engine', 'core_db_engine_idx');
            $table->index('env', 'core_db_env_idx');
            $table->index('vc_stage', 'core_db_vc_stage_idx');
            $table->index('vc_industry', 'core_db_vc_industry_idx');
            $table->index('vc_subindustry', 'core_db_vc_subindustry_idx');
            // Legacy fields often used in views
            $table->index('environment', 'core_db_environment_idx');
            $table->index('platform', 'core_db_platform_idx');
            $table->index('owner', 'core_db_owner_idx');
            $table->index('status', 'core_db_status_idx');
        });
    }

    public function down(): void
    {
        Schema::table('core_databases', function (Blueprint $table) {
            $table->dropIndex('core_db_tier_idx');
            $table->dropIndex('core_db_engine_idx');
            $table->dropIndex('core_db_env_idx');
            $table->dropIndex('core_db_vc_stage_idx');
            $table->dropIndex('core_db_vc_industry_idx');
            $table->dropIndex('core_db_vc_subindustry_idx');
            $table->dropIndex('core_db_environment_idx');
            $table->dropIndex('core_db_platform_idx');
            $table->dropIndex('core_db_owner_idx');
            $table->dropIndex('core_db_status_idx');
        });
    }
};
