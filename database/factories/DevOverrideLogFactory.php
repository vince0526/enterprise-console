<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\DevOverrideLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<DevOverrideLog> */
class DevOverrideLogFactory extends Factory
{
    protected $model = DevOverrideLog::class;

    /** @return array{user_id:int,email:string,ip:string|null} */
    public function definition(): array
    {
        return [
            'user_id' => 1, // default; tests may override
            'email' => $this->faker->unique()->safeEmail(),
            'ip' => $this->faker->ipv4(),
        ];
    }
}
