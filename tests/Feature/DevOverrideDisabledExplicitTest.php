<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Config;

use function Pest\Laravel\postJson;

it('returns 403 when feature flag disabled and token supplied', function (): void {
    Config::set('dev_override.enabled', false);
    Config::set('dev_override.token', 'abc');
    Config::set('dev_override.email', 'dev@example.test');

    $response = postJson('/dev-override', [
        'token' => 'abc',
    ]);

    $response->assertStatus(403);
});
