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
            $table->string('tier')->nullable()->after('status');
            $table->string('tax_path')->nullable()->after('tier');
            $table->string('vc_stage')->nullable()->after('tax_path');
            $table->string('vc_industry')->nullable()->after('vc_stage');
            $table->string('vc_subindustry')->nullable()->after('vc_industry');
            $table->json('cross_enablers')->nullable()->after('vc_subindustry');
            $table->json('functional_scopes')->nullable()->after('cross_enablers');
            $table->string('engine')->nullable()->after('functional_scopes');
            $table->string('env')->nullable()->after('engine');
            $table->string('owner_email')->nullable()->after('owner');
        });

        // Backfill engine and env from platform/environment if present
        DB::table('core_databases')->select('id', 'platform', 'environment')->orderBy('id')->chunkById(500, function ($rows) {
            foreach ($rows as $row) {
                $engine = $row->platform;
                $env = match ($row->environment) {
                    'Production' => 'Prod',
                    'Staging' => 'UAT',
                    'Development' => 'Dev',
                    default => $row->environment,
                };
                DB::table('core_databases')->where('id', $row->id)->update([
                    'engine' => $engine,
                    'env' => $env,
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('core_databases', function (Blueprint $table) {
            $table->dropColumn([
                'tier',
                'tax_path',
                'vc_stage',
                'vc_industry',
                'vc_subindustry',
                'cross_enablers',
                'functional_scopes',
                'engine',
                'env',
                'owner_email',
            ]);
        });
    }
};
