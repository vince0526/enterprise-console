<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\PublicGood;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<PublicGood> */
class PublicGoodFactory extends Factory
{
    protected $model = PublicGood::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(2, true),
        ];
    }
}
