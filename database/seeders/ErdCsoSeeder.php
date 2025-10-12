<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CsoSuperCategory;
use App\Models\CsoType;
use Illuminate\Database\Seeder;

class ErdCsoSeeder extends Seeder
{
    public function run(): void
    {
        $super = [
            'Non-Governmental Organizations',
            'Community-Based Organizations',
            'Faith-Based Organizations',
            'Professional Associations',
            'Trade Unions',
            'Cooperatives',
            'Foundations',
            'Social Enterprises',
        ];
        $typeMap = [
            'Non-Governmental Organizations' => [
                'Advocacy NGO',
                'Service NGO',
                'Environmental NGO',
                'Human Rights NGO',
                'Health NGO',
            ],
            'Community-Based Organizations' => [
                'Residents Association',
                'Youth Group',
                'Women’s Group',
                'Farmers Group',
                'Neighborhood Watch',
            ],
            'Faith-Based Organizations' => [
                'Church Group',
                'Relief Arm',
                'Missionary Society',
                'Interfaith Council',
            ],
            'Professional Associations' => [
                'Medical Association',
                'Teachers Union',
                'Bar Association',
                'Engineers Society',
                'Accountants Institute',
            ],
            'Trade Unions' => [
                'Manufacturing Workers Union',
                'Transport Workers Union',
                'Public Sector Union',
            ],
            'Cooperatives' => [
                'Savings & Credit Cooperative',
                'Agricultural Cooperative',
                'Housing Cooperative',
            ],
            'Foundations' => [
                'Corporate Foundation',
                'Family Foundation',
                'Community Foundation',
            ],
            'Social Enterprises' => [
                'Impact Venture',
                'Fair-Trade Enterprise',
                'B-Corp',
            ],
        ];
        foreach ($super as $name) {
            $sc = CsoSuperCategory::firstOrCreate(['name' => $name]);
            foreach ($typeMap[$name] as $t) {
                CsoType::firstOrCreate(['cso_super_category_id' => $sc->id, 'name' => $t]);
            }
        }
    }
}
