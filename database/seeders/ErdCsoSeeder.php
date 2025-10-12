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
        ];
        $typeMap = [
            'Non-Governmental Organizations' => ['Advocacy NGO', 'Service NGO'],
            'Community-Based Organizations' => ['Residents Association', 'Youth Group'],
            'Faith-Based Organizations' => ['Church Group', 'Relief Arm'],
            'Professional Associations' => ['Medical Association', 'Teachers Union'],
        ];
        foreach ($super as $name) {
            $sc = CsoSuperCategory::firstOrCreate(['name' => $name]);
            foreach ($typeMap[$name] as $t) {
                CsoType::firstOrCreate(['cso_super_category_id' => $sc->id, 'name' => $t]);
            }
        }
    }
}
