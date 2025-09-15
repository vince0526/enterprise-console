<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Tests\Support\WithDevOverride;
use Tests\TestCase;

class DevOverrideDisabledTest extends TestCase
{
    use WithDevOverride;

    public function test_dev_override_disabled_blocks_access(): void
    {
        Artisan::call('migrate', ['--force' => true]);

        // Start from enabled baseline then disable to ensure path works from either state.
        $this->enableDevOverride('test-token');
        config(['dev_override.enabled' => false]);

        $response = $this->postJson('/dev-override', ['token' => 'test-token']);

        $response->assertStatus(403)
            ->assertJson(fn ($json) => $json
                ->where('success', false)
                ->where('message', 'dev override disabled'));
    }
}
