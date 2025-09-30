<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CoreDatabase;
use Illuminate\Database\Seeder;

class CoreDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'name' => 'Core Identity',
                'environment' => 'Production',
                'platform' => 'PostgreSQL',
                'owner' => 'Platform Team',
                'lifecycle' => 'Long-lived',
                'linked_connection' => 'prod-identity',
                'description' => 'Central auth and directory services',
                'status' => 'healthy',
            ],
            [
                'name' => 'Core Billing',
                'environment' => 'Staging',
                'platform' => 'MySQL',
                'owner' => 'Finance Ops',
                'lifecycle' => 'Long-lived',
                'linked_connection' => 'stage-billing',
                'description' => 'Billing and invoicing data',
                'status' => 'healthy',
            ],
        ];

        foreach ($rows as $r) {
            CoreDatabase::query()->firstOrCreate(['name' => $r['name']], $r);
        }
    }
}
