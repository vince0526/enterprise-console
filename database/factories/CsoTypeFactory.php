<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CsoSuperCategory;
use App\Models\CsoType;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CsoType> */
class CsoTypeFactory extends Factory
{
    protected $model = CsoType::class;

    public function definition(): array
    {
        return [
            'cso_super_category_id' => CsoSuperCategory::factory(),
            'name' => $this->faker->unique()->words(2, true),
        ];
    }
}
