<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Industry;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Industry> */
class IndustryFactory extends Factory
{
    protected $model = Industry::class;

    public function definition(): array
    {
        return [
            'industry_name' => $this->faker->unique()->words(2, true),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
