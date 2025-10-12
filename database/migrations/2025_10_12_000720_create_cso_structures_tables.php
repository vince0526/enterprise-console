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
        // CSO_SUPER_CATEGORY
        Schema::create('cso_super_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->timestamps();
        });

        // CSO_TYPE
        Schema::create('cso_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cso_super_category_id');
            $table->string('name');
            $table->timestamps();

            $table->foreign('cso_super_category_id')->references('id')->on('cso_super_categories')->cascadeOnDelete();
            $table->index(['cso_super_category_id']);
        });

        // CSO_TYPE_PUBLIC_GOOD_MAP
        Schema::create('cso_type_public_good', function (Blueprint $table) {
            $table->unsignedBigInteger('cso_type_id');
            $table->unsignedBigInteger('pg_id');
            $table->primary(['cso_type_id', 'pg_id']);

            $table->foreign('cso_type_id')->references('id')->on('cso_types')->cascadeOnDelete();
            $table->foreign('pg_id')->references('id')->on('public_goods')->cascadeOnDelete();
        });

        // CSO_TYPE_VALUE_CHAIN_STAGE_MAP
        Schema::create('cso_type_stage', function (Blueprint $table) {
            $table->unsignedBigInteger('cso_type_id');
            $table->unsignedBigInteger('stage_id');
            $table->primary(['cso_type_id', 'stage_id']);

            $table->foreign('cso_type_id')->references('id')->on('cso_types')->cascadeOnDelete();
            $table->foreign('stage_id')->references('id')->on('value_chain_stages')->cascadeOnDelete();
        });

        // CSO_TYPE_REGULATOR_MAP
        Schema::create('cso_type_regulator', function (Blueprint $table) {
            $table->unsignedBigInteger('cso_type_id');
            $table->unsignedBigInteger('domain_id');
            $table->unsignedBigInteger('org_id');
            $table->primary(['cso_type_id', 'domain_id', 'org_id']);

            $table->foreign('cso_type_id')->references('id')->on('cso_types')->cascadeOnDelete();
            $table->foreign('domain_id')->references('id')->on('regulation_domains')->cascadeOnDelete();
            $table->foreign('org_id')->references('id')->on('gov_orgs')->cascadeOnDelete();
        });

        // Amend provider_registries to attach CSO and Industry FKs now that those tables exist
        // Add foreign keys if supported by the driver (SQLite cannot add FKs via ALTER TABLE)
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('provider_registries', function (Blueprint $table) {
                if (Schema::hasColumn('provider_registries', 'cso_type_id')) {
                    $table->foreign('cso_type_id')->references('id')->on('cso_types')->nullOnDelete();
                }
                if (Schema::hasColumn('provider_registries', 'industry_id')) {
                    $table->foreign('industry_id')->references('id')->on('industries')->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('provider_registries', function (Blueprint $table) {
                if (Schema::hasColumn('provider_registries', 'cso_type_id')) {
                    $table->dropForeign(['cso_type_id']);
                }
                if (Schema::hasColumn('provider_registries', 'industry_id')) {
                    $table->dropForeign(['industry_id']);
                }
            });
        }

        Schema::dropIfExists('cso_type_regulator');
        Schema::dropIfExists('cso_type_stage');
        Schema::dropIfExists('cso_type_public_good');
        Schema::dropIfExists('cso_types');
        Schema::dropIfExists('cso_super_categories');
    }
};
