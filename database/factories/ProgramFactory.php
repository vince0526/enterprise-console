<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Program> */
class ProgramFactory extends Factory
{
    protected $model = Program::class;

    public function definition(): array
    {
        return [
            'pg_id' => $this->faker->unique()->uuid(),
            'lead_org_id' => null,
            'delivery_mode' => $this->faker->randomElement(['Direct', 'Through Partners']),
            'benefit_type' => $this->faker->randomElement(['Cash', 'In-kind', 'Service']),
            'status' => $this->faker->randomElement(['Active', 'Paused', 'Ended']),
        ];
    }
}
