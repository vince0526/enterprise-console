<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('core_databases', function (Blueprint $table) {
            // Value Chain
            $table->unsignedBigInteger('stage_id')->nullable()->after('vc_subindustry');
            $table->unsignedBigInteger('industry_id')->nullable()->after('stage_id');
            $table->unsignedBigInteger('subindustry_id')->nullable()->after('industry_id');
            // Public Goods & Governance
            $table->unsignedBigInteger('pg_id')->nullable()->after('subindustry_id');
            $table->unsignedBigInteger('lead_org_id')->nullable()->after('pg_id');
            $table->unsignedBigInteger('program_id')->nullable()->after('lead_org_id');
            // CSO
            $table->unsignedBigInteger('cso_super_category_id')->nullable()->after('program_id');
            $table->unsignedBigInteger('cso_type_id')->nullable()->after('cso_super_category_id');

            $table->index(['stage_id', 'industry_id', 'subindustry_id']);
            $table->index(['pg_id', 'lead_org_id', 'program_id']);
            $table->index(['cso_super_category_id', 'cso_type_id']);
        });

        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('core_databases', function (Blueprint $table) {
                $table->foreign('stage_id')->references('id')->on('value_chain_stages')->nullOnDelete();
                $table->foreign('industry_id')->references('id')->on('industries')->nullOnDelete();
                $table->foreign('subindustry_id')->references('id')->on('subindustries')->nullOnDelete();
                $table->foreign('pg_id')->references('id')->on('public_goods')->nullOnDelete();
                $table->foreign('lead_org_id')->references('id')->on('gov_orgs')->nullOnDelete();
                $table->foreign('program_id')->references('id')->on('programs')->nullOnDelete();
                $table->foreign('cso_super_category_id')->references('id')->on('cso_super_categories')->nullOnDelete();
                $table->foreign('cso_type_id')->references('id')->on('cso_types')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('core_databases', function (Blueprint $table) {
                $table->dropForeign(['stage_id']);
                $table->dropForeign(['industry_id']);
                $table->dropForeign(['subindustry_id']);
                $table->dropForeign(['pg_id']);
                $table->dropForeign(['lead_org_id']);
                $table->dropForeign(['program_id']);
                $table->dropForeign(['cso_super_category_id']);
                $table->dropForeign(['cso_type_id']);
            });
        }
        Schema::table('core_databases', function (Blueprint $table) {
            $table->dropIndex(['stage_id', 'industry_id', 'subindustry_id']);
            $table->dropIndex(['pg_id', 'lead_org_id', 'program_id']);
            $table->dropIndex(['cso_super_category_id', 'cso_type_id']);
            $table->dropColumn([
                'stage_id',
                'industry_id',
                'subindustry_id',
                'pg_id',
                'lead_org_id',
                'program_id',
                'cso_super_category_id',
                'cso_type_id',
            ]);
        });
    }
};
