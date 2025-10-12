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
        // Government Orgs (by domain)
        $orgs = [
            'Health' => [
                'ministry' => 'Ministry of Health',
                'agency' => 'National Health Agency',
            ],
            'Education' => [
                'ministry' => 'Ministry of Education',
                'agency' => 'National Education Service',
            ],
            'Transport' => [
                'ministry' => 'Ministry of Transport',
                'agency' => 'National Transport Authority',
            ],
            'Water & Sanitation' => [
                'ministry' => 'Ministry of Water & Sanitation',
                'agency' => 'National Water Service',
            ],
            'Governance' => [
                'ministry' => 'Ministry of Interior',
                'agency' => 'Civil Service Commission',
            ],
        ];

        $programSpecs = [
            // Health
            [
                'pg' => 'Health',
                'delivery' => 'Direct',
                'benefit' => 'Service',
                'status' => 'Active',
                'stages' => ['Service Delivery (End-User Services)', 'After-Sales, Reverse & End-of-Life'],
            ],
            // Education
            [
                'pg' => 'Education',
                'delivery' => 'Direct',
                'benefit' => 'Service',
                'status' => 'Active',
                'stages' => ['Service Delivery (End-User Services)'],
            ],
            // Transport
            [
                'pg' => 'Transport',
                'delivery' => 'Infrastructure',
                'benefit' => 'Public Asset',
                'status' => 'Active',
                'stages' => ['Logistics, Ports & Fulfillment', 'Market Access, Trading & Wholesale'],
            ],
            // Water & Sanitation
            [
                'pg' => 'Water & Sanitation',
                'delivery' => 'Direct',
                'benefit' => 'Service',
                'status' => 'Active',
                'stages' => [
                    'Utilities' /* mapped via taxonomy stage: use Service Delivery to align */,
                    'Service Delivery (End-User Services)',
                ],
            ],
            // Governance
            [
                'pg' => 'Governance',
                'delivery' => 'Policy',
                'benefit' => 'Regulatory',
                'status' => 'Active',
                'stages' => ['Market Access, Trading & Wholesale'],
            ],
        ];

        foreach ($orgs as $pgName => $pair) {
            $ministry = GovOrg::firstOrCreate(['name' => $pair['ministry']], [
                'org_type' => 'Ministry',
                'jurisdiction' => 'National',
                'is_soe' => false,
            ]);
            $agency = GovOrg::firstOrCreate(['name' => $pair['agency']], [
                'org_type' => 'Agency',
                'jurisdiction' => 'National',
                'is_soe' => false,
                'parent_org_id' => $ministry->id,
            ]);
        }

        foreach ($programSpecs as $spec) {
            $pg = PublicGood::firstOrCreate(['name' => $spec['pg']]);
            $lead = GovOrg::where('name', $orgs[$spec['pg']]['agency'])->first();
            $program = Program::firstOrCreate([
                'pg_id' => $pg->id,
                'lead_org_id' => $lead?->id,
                'delivery_mode' => $spec['delivery'],
                'benefit_type' => $spec['benefit'],
                'status' => $spec['status'],
            ]);
            foreach ($spec['stages'] as $name) {
                // Ensure stage exists (skip any accidental non-stage labels)
                $stage = ValueChainStage::firstOrCreate(['stage_name' => $name]);
                DB::table('program_stage')->updateOrInsert([
                    'program_id' => $program->id,
                    'stage_id' => $stage->id,
                ], []);
            }
        }
    }
}
