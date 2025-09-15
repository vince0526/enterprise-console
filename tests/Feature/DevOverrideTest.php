<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Tests\Support\WithDevOverride;
use Tests\TestCase;

class DevOverrideTest extends TestCase
{
    use WithDevOverride;

    public function test_dev_override_creates_user_and_returns_success(): void
    {
        // Run all pending migrations to ensure required tables (users, dev_override_logs) exist
        Artisan::call('migrate', ['--force' => true]);

        // ensure the env token is set for the test
        // Use a non-sensitive test token set in the test environment.
        $this->enableDevOverride();
        $response = $this->postJson('/dev-override', [
            'token' => 'test-dev-token',
        ]);

        $response->assertOk()
            ->assertJson(fn ($json) => $json->where('success', true)->has('redirect'));

        $this->assertDatabaseHas('dev_override_logs', [
            'email' => 'dev@example.com',
        ]);
    }
}
