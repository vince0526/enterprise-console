<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Industry;
use App\Models\PublicGood;
use App\Models\Subindustry;
use App\Models\ValueChainStage;
use Illuminate\Database\Seeder;

class ErdTaxonomySeeder extends Seeder
{
    public function run(): void
    {
        // Value Chain Stages
        $stages = [
            'Input Sourcing', 'Production', 'Distribution', 'Retail', 'After-Sales',
        ];
        foreach ($stages as $name) {
            ValueChainStage::firstOrCreate(['stage_name' => $name]);
        }

        // Industries & Subindustries (illustrative subset)
        $industryMap = [
            'Agriculture' => ['Crops', 'Livestock', 'AgriTech'],
            'Manufacturing' => ['Automotive', 'Electronics', 'Pharmaceuticals'],
            'Media' => ['Broadcasting', 'Digital/Streaming', 'Newsrooms'],
            'Financial Services' => ['Banking', 'Insurance', 'Payments'],
        ];
        foreach ($industryMap as $industryName => $subs) {
            $industry = Industry::firstOrCreate(['industry_name' => $industryName]);
            foreach ($subs as $sub) {
                Subindustry::firstOrCreate([
                    'industry_id' => $industry->id,
                    'subindustry_name' => $sub,
                ]);
            }
        }

        // Public Goods (subset)
        foreach (['Health', 'Education', 'Transport', 'Water & Sanitation'] as $pg) {
            PublicGood::firstOrCreate(['name' => $pg]);
        }
    }
}
