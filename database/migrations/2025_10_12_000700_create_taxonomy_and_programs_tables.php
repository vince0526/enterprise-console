<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // INDUSTRY
        Schema::create('industries', function (Blueprint $table) {
            $table->bigIncrements('id'); // industry_id PK
            $table->string('industry_name');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // SUBINDUSTRY
        Schema::create('subindustries', function (Blueprint $table) {
            $table->bigIncrements('id'); // subindustry_id PK
            $table->unsignedBigInteger('industry_id');
            $table->string('subindustry_name');
            $table->timestamps();

            $table->foreign('industry_id')->references('id')->on('industries')->cascadeOnDelete();
            $table->index(['industry_id']);
        });

        // VALUE_CHAIN_STAGE
        Schema::create('value_chain_stages', function (Blueprint $table) {
            $table->bigIncrements('id'); // stage_id PK
            $table->string('stage_name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // PUBLIC_GOOD
        Schema::create('public_goods', function (Blueprint $table) {
            $table->bigIncrements('id'); // pg_id PK
            $table->string('name');
            $table->timestamps();
        });

        // GOV_ORG (self-referencing parent)
        Schema::create('gov_orgs', function (Blueprint $table) {
            $table->bigIncrements('id'); // org_id PK
            $table->string('name');
            $table->string('org_type')->nullable();
            $table->string('jurisdiction')->nullable();
            $table->boolean('is_soe')->default(false);
            $table->unsignedBigInteger('parent_org_id')->nullable();
            $table->timestamps();

            $table->foreign('parent_org_id')->references('id')->on('gov_orgs')->nullOnDelete();
            $table->index(['org_type', 'jurisdiction']);
        });

        // PROGRAM
        Schema::create('programs', function (Blueprint $table) {
            $table->bigIncrements('id'); // program_id PK
            $table->unsignedBigInteger('pg_id');
            $table->unsignedBigInteger('lead_org_id');
            $table->string('delivery_mode')->nullable();
            $table->string('benefit_type')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();

            $table->foreign('pg_id')->references('id')->on('public_goods')->cascadeOnDelete();
            $table->foreign('lead_org_id')->references('id')->on('gov_orgs')->cascadeOnDelete();
            $table->index(['pg_id', 'lead_org_id']);
        });

        // SERVICE_CHANNEL
        Schema::create('service_channels', function (Blueprint $table) {
            $table->bigIncrements('id'); // channel_id PK
            $table->string('name');
            $table->string('channel_type')->nullable();
            $table->timestamps();
        });

        // FACILITY_SITE
        Schema::create('facility_sites', function (Blueprint $table) {
            $table->bigIncrements('id'); // site_id PK
            $table->unsignedBigInteger('channel_id');
            $table->unsignedBigInteger('operator_org_id');
            $table->string('site_type')->nullable();
            $table->string('geo_ref')->nullable();
            $table->timestamps();

            $table->foreign('channel_id')->references('id')->on('service_channels')->cascadeOnDelete();
            $table->foreign('operator_org_id')->references('id')->on('gov_orgs')->cascadeOnDelete();
            $table->index(['channel_id', 'operator_org_id']);
        });

        // PROVIDER_REGISTRY
        Schema::create('provider_registries', function (Blueprint $table) {
            $table->bigIncrements('id'); // provider_id PK
            $table->string('provider_type')->nullable();
            $table->string('site_link_type')->nullable();
            $table->unsignedBigInteger('org_id');
            $table->unsignedBigInteger('cso_type_id')->nullable();
            $table->unsignedBigInteger('industry_id')->nullable();
            $table->string('accreditation_status')->nullable();
            $table->timestamps();

            $table->foreign('org_id')->references('id')->on('gov_orgs')->cascadeOnDelete();
            // cso_type_id and industry_id FKs will be added after CSO tables exist (later migration)
            $table->index(['org_id', 'cso_type_id', 'industry_id']);
        });

        // BENEFICIARY_GROUP
        Schema::create('beneficiary_groups', function (Blueprint $table) {
            $table->bigIncrements('id'); // group_id PK
            $table->string('name');
            $table->text('criteria')->nullable();
            $table->timestamps();
        });

        // FUNDING_SOURCE
        Schema::create('funding_sources', function (Blueprint $table) {
            $table->bigIncrements('id'); // source_id PK
            $table->string('name');
            $table->string('type')->nullable();
            $table->timestamps();
        });

        // KPI_LIBRARY
        Schema::create('kpis', function (Blueprint $table) {
            $table->bigIncrements('id'); // kpi_id PK
            $table->string('kpi_name');
            $table->timestamps();
        });

        // GRIEVANCE_CHANNEL
        Schema::create('grievance_channels', function (Blueprint $table) {
            $table->bigIncrements('id'); // gch_id PK
            $table->unsignedBigInteger('program_id');
            $table->string('channel_type');
            $table->timestamps();

            $table->foreign('program_id')->references('id')->on('programs')->cascadeOnDelete();
            $table->index(['program_id']);
        });

        // GRIEVANCE_CASE
        Schema::create('grievance_cases', function (Blueprint $table) {
            $table->bigIncrements('id'); // case_id PK
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('gch_id');
            $table->string('status')->nullable();
            $table->timestamps();

            $table->foreign('program_id')->references('id')->on('programs')->cascadeOnDelete();
            $table->foreign('gch_id')->references('id')->on('grievance_channels')->cascadeOnDelete();
            $table->index(['program_id', 'gch_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grievance_cases');
        Schema::dropIfExists('grievance_channels');
        Schema::dropIfExists('kpis');
        Schema::dropIfExists('funding_sources');
        Schema::dropIfExists('beneficiary_groups');
        Schema::dropIfExists('provider_registries');
        Schema::dropIfExists('facility_sites');
        Schema::dropIfExists('service_channels');
        Schema::dropIfExists('programs');
        Schema::dropIfExists('gov_orgs');
        Schema::dropIfExists('public_goods');
        Schema::dropIfExists('value_chain_stages');
        Schema::dropIfExists('subindustries');
        Schema::dropIfExists('industries');
    }
};
