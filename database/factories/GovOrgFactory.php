<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\GovOrg;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<GovOrg> */
class GovOrgFactory extends Factory
{
    protected $model = GovOrg::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'org_type' => $this->faker->randomElement(['Ministry', 'Agency', 'Department']),
            'jurisdiction' => $this->faker->randomElement(['National', 'State', 'Local']),
            'is_soe' => $this->faker->boolean(),
            'parent_org_id' => null,
        ];
    }
}
