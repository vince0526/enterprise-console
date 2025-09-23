<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Support\WithDevOverride;
use Tests\TestCase;

class DevOverrideMissingTokenTest extends TestCase
{
    use WithDevOverride;

    public function test_missing_token_returns_422(): void
    {
        $this->enableDevOverrideFlagOnly();

        $response = $this->postJson('/dev-override', [
            'email' => 'dev@example.com',
            'token' => 'anything', // Provided but config token intentionally empty
        ]);

        $response->assertStatus(422)
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('success', false)
                ->where('message', 'dev override token not configured'));
    }
}
