<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CoreDatabase;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CoreDatabase>
 */
class CoreDatabaseFactory extends Factory
{
    protected $model = CoreDatabase::class;

    public function definition(): array
    {
        $name = 'db_'.fake()->unique()->bothify('????_##');
        $platform = fake()->randomElement(['PostgreSQL', 'MySQL', 'SQL Server']);
        $environment = fake()->randomElement(['Production', 'Staging', 'Development']);
        $engine = $platform; // normalized
        $env = match ($environment) {
            'Production' => 'Prod',
            'Staging' => 'UAT',
            'Development' => 'Dev',
            default => 'Dev',
        };

        return [
            'name' => $name,
            'environment' => $environment,
            'platform' => $platform,
            'engine' => $engine,
            'env' => $env,
            'owner' => fake()->company(),
            'owner_email' => fake()->safeEmail(),
            'lifecycle' => fake()->randomElement(['Long-lived', 'Temporary', 'Archived']),
            'linked_connection' => null,
            'description' => fake()->sentence(8),
            'status' => fake()->randomElement(['healthy', 'warning', 'info']),
            'tier' => fake()->randomElement(['Value Chain', 'Public Goods & Governance', 'CSO', 'Media', 'Financial']),
            'tax_path' => null,
            'vc_stage' => null,
            'vc_industry' => null,
            'vc_subindustry' => null,
            'cross_enablers' => [],
            'functional_scopes' => [],
        ];
    }
}
