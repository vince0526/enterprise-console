<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CsoSuperCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CsoSuperCategory> */
class CsoSuperCategoryFactory extends Factory
{
    protected $model = CsoSuperCategory::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(2, true),
        ];
    }
}
