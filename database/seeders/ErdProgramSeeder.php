<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\GovOrg;
use App\Models\Program;
use App\Models\PublicGood;
use App\Models\ValueChainStage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ErdProgramSeeder extends Seeder
{
    public function run(): void
    {
        // Government Orgs
        $ministry = GovOrg::firstOrCreate(['name' => 'Ministry of Health'], [
            'org_type' => 'Ministry', 'jurisdiction' => 'National', 'is_soe' => false,
        ]);
        $agency = GovOrg::firstOrCreate(['name' => 'National Health Agency'], [
            'org_type' => 'Agency', 'jurisdiction' => 'National', 'is_soe' => false,
            'parent_org_id' => $ministry->id,
        ]);

        // Public Good
        $pg = PublicGood::firstOrCreate(['name' => 'Health']);

        // Program
        $program = Program::firstOrCreate([
            'pg_id' => $pg->id,
            'lead_org_id' => $agency->id,
            'delivery_mode' => 'Direct',
            'benefit_type' => 'Service',
            'status' => 'Active',
        ]);

        // Map program to a couple of value chain stages
        $stageNames = ['Distribution', 'After-Sales'];
        foreach ($stageNames as $name) {
            $stage = ValueChainStage::firstOrCreate(['stage_name' => $name]);
            DB::table('program_stage')->updateOrInsert([
                'program_id' => $program->id,
                'stage_id' => $stage->id,
            ], []);
        }
    }
}
