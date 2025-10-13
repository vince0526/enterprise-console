<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Industry;
use App\Models\Subindustry;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Subindustry> */
class SubindustryFactory extends Factory
{
    protected $model = Subindustry::class;

    public function definition(): array
    {
        return [
            'industry_id' => Industry::factory(),
            'subindustry_name' => $this->faker->unique()->words(3, true),
        ];
    }
}
