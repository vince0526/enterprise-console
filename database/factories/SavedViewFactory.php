<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\SavedView;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SavedView>
 */
class SavedViewFactory extends Factory
{
    protected $model = SavedView::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'context' => 'core_databases',
            'name' => $this->faker->unique()->words(2, true),
            'filters' => ['engine' => 'PostgreSQL'],
        ];
    }
}
