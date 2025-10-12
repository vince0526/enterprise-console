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
        // Value Chain Stages (aligned with UI wizard)
        $stages = [
            'Resource Extraction (Primary)',
            'Primary Processing (Materials)',
            'Secondary Manufacturing & Assembly',
            'Market Access, Trading & Wholesale',
            'Logistics, Ports & Fulfillment',
            'Retail & Direct-to-Consumer (Goods)',
            'Service Delivery (End-User Services)',
            'After-Sales, Reverse & End-of-Life',
        ];
        foreach ($stages as $name) {
            ValueChainStage::firstOrCreate(['stage_name' => $name]);
        }

        // Industries & Subindustries (aligned with VC mapping in UI)
        $industryMap = [
            'Automotive' => ['Vehicle Assembly', 'Auto Parts', 'EV Batteries'],
            'Aerospace' => ['Aircraft Assembly', 'MRO', 'Avionics'],
            'Electronics' => ['Semiconductors', 'Consumer Devices', 'Industrial IoT'],
            'Agriculture' => ['Row Crops', 'Horticulture', 'Livestock'],
            'Fisheries' => ['Aquaculture', 'Wild Capture', 'Cold Chain'],
            'Mining' => ['Open Pit', 'Underground', 'Mineral Processing'],
            'Oil & Gas' => ['Upstream', 'Midstream', 'Downstream'],
            'Chemicals' => ['Basic Chemicals', 'Specialty', 'Fertilizers'],
            'Pharmaceuticals' => ['APIs', 'Formulation', 'Distribution'],
            'Textiles & Apparel' => ['Spinning', 'Weaving', 'Garments'],
            'Food & Beverage' => ['Meat Processing', 'Dairy', 'Beverages'],
            'Construction' => ['Cement', 'Building Materials', 'Contracting'],
            'Utilities' => ['Power Generation', 'Transmission', 'Distribution'],
            'Logistics' => ['Courier', 'Freight Forwarding', 'Warehousing', 'Air Cargo'],
            'Wholesale & Trading' => ['Commodity Trading', 'Pharma Wholesale', 'B2B Marketplace'],
            'Retail' => ['Grocery', 'General Merchandise', 'E-commerce'],
            'Hospitality' => ['Hotels', 'Restaurants', 'Catering'],
            'Travel & Tourism' => ['Airlines', 'Cruise', 'Tour Operators'],
            'Healthcare' => ['Hospitals', 'Clinics', 'Pharmacies'],
            'Education' => ['K-12', 'Universities', 'Vocational'],
            'Telecommunications' => ['Mobile', 'Fixed Broadband', 'Data Centers'],
            'ITServices' => ['Software Dev', 'Managed Services', 'Cloud'],
            'Waste & Recycling' => ['Solid Waste', 'Recycling', 'E-waste'],
            'Metals' => ['Steel', 'Non-ferrous', 'Foundry'],
            'Wood & Paper' => ['Forestry', 'Pulp', 'Paper & Packaging'],
            'Plastics' => ['Resins', 'Molding', 'Recycling'],
            'Maritime' => ['Shipbuilding', 'Ports', 'Shipping Lines'],
            'Furniture' => ['Residential', 'Office', 'Fixtures'],
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

        // Public Goods (expanded)
        foreach (['Health', 'Education', 'Transport', 'Water & Sanitation', 'Governance'] as $pg) {
            PublicGood::firstOrCreate(['name' => $pg]);
        }
    }
}
