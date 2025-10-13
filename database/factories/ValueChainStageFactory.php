<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ValueChainStage;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ValueChainStage> */
class ValueChainStageFactory extends Factory
{
    protected $model = ValueChainStage::class;

    public function definition(): array
    {
        return [
            'stage_name' => $this->faker->unique()->word(),
            'description' => $this->faker->optional()->sentence(),
        ];
    }
}
