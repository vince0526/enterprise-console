<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // REGULATION_DOMAIN
        Schema::create('regulation_domains', function (Blueprint $table) {
            $table->bigIncrements('id'); // domain_id PK
            $table->string('name');
            $table->timestamps();
        });

        // LEGAL_INSTRUMENT
        Schema::create('legal_instruments', function (Blueprint $table) {
            $table->bigIncrements('id'); // instr_id PK
            $table->unsignedBigInteger('domain_id');
            $table->string('jurisdiction');
            $table->string('title');
            $table->string('citation')->nullable();
            $table->date('effective_date')->nullable();
            $table->timestamps();

            $table->foreign('domain_id')->references('id')->on('regulation_domains')->cascadeOnDelete();
            $table->index(['domain_id', 'jurisdiction']);
        });

        // REGULATED_SECTOR
        Schema::create('regulated_sectors', function (Blueprint $table) {
            $table->bigIncrements('id'); // reg_sec_id PK
            $table->unsignedBigInteger('domain_id');
            $table->unsignedBigInteger('industry_id');
            $table->unsignedBigInteger('subindustry_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('domain_id')->references('id')->on('regulation_domains')->cascadeOnDelete();
            $table->foreign('industry_id')->references('id')->on('industries')->cascadeOnDelete();
            $table->foreign('subindustry_id')->references('id')->on('subindustries')->nullOnDelete();
            $table->index(['domain_id', 'industry_id', 'subindustry_id']);
        });

        // COMPLIANCE_OBLIGATION
        Schema::create('compliance_obligations', function (Blueprint $table) {
            $table->bigIncrements('id'); // obligation_id PK
            $table->unsignedBigInteger('domain_id');
            $table->text('description');
            $table->string('control_type')->nullable();
            $table->timestamps();

            $table->foreign('domain_id')->references('id')->on('regulation_domains')->cascadeOnDelete();
            $table->index(['domain_id']);
        });

        // PROGRAM_OBLIGATION_MAP (pivot)
        Schema::create('program_obligation', function (Blueprint $table) {
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('obligation_id');
            $table->primary(['program_id', 'obligation_id']);

            $table->foreign('program_id')->references('id')->on('programs')->cascadeOnDelete();
            $table->foreign('obligation_id')->references('id')->on('compliance_obligations')->cascadeOnDelete();
        });

        // PROGRAM_SITE_MAP (pivot)
        Schema::create('program_site', function (Blueprint $table) {
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('site_id');
            $table->primary(['program_id', 'site_id']);

            $table->foreign('program_id')->references('id')->on('programs')->cascadeOnDelete();
            $table->foreign('site_id')->references('id')->on('facility_sites')->cascadeOnDelete();
        });

        // PROGRAM_STAGE_MAP (pivot)
        Schema::create('program_stage', function (Blueprint $table) {
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('stage_id');
            $table->primary(['program_id', 'stage_id']);

            $table->foreign('program_id')->references('id')->on('programs')->cascadeOnDelete();
            $table->foreign('stage_id')->references('id')->on('value_chain_stages')->cascadeOnDelete();
        });

        // PROGRAM_ELIGIBILITY (pivot)
        Schema::create('program_eligibility', function (Blueprint $table) {
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('group_id');
            $table->text('rule_ref')->nullable();
            $table->primary(['program_id', 'group_id']);

            $table->foreign('program_id')->references('id')->on('programs')->cascadeOnDelete();
            $table->foreign('group_id')->references('id')->on('beneficiary_groups')->cascadeOnDelete();
        });

        // PROGRAM_FUNDING (pivot with extra data)
        Schema::create('program_funding', function (Blueprint $table) {
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('source_id');
            $table->decimal('amount', 18, 2)->nullable();
            $table->integer('fiscal_year');
            $table->primary(['program_id', 'source_id', 'fiscal_year']);

            $table->foreign('program_id')->references('id')->on('programs')->cascadeOnDelete();
            $table->foreign('source_id')->references('id')->on('funding_sources')->cascadeOnDelete();
        });

        // PROGRAM_KPI (pivot with extra data)
        Schema::create('program_kpi', function (Blueprint $table) {
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('kpi_id');
            $table->string('target')->nullable();
            $table->string('baseline')->nullable();
            $table->string('unit')->nullable();
            $table->primary(['program_id', 'kpi_id']);

            $table->foreign('program_id')->references('id')->on('programs')->cascadeOnDelete();
            $table->foreign('kpi_id')->references('id')->on('kpis')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_kpi');
        Schema::dropIfExists('program_funding');
        Schema::dropIfExists('program_eligibility');
        Schema::dropIfExists('program_stage');
        Schema::dropIfExists('program_site');
        Schema::dropIfExists('program_obligation');
        Schema::dropIfExists('compliance_obligations');
        Schema::dropIfExists('regulated_sectors');
        Schema::dropIfExists('legal_instruments');
        Schema::dropIfExists('regulation_domains');
    }
};
