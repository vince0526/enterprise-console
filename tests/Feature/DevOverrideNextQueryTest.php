<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Config;

use function Pest\Laravel\postJson;

beforeEach(function (): void {
    Config::set('dev_override.enabled', true);
    Config::set('dev_override.token', 'x');
    Config::set('dev_override.email', 'dev@example.test');
});

it('allows next path with query string', function (): void {
    $response = postJson('/dev-override', [
        'token' => 'x',
        'next' => '/profile?tab=security&page=2',
    ]);

    $response->assertOk()->assertJson(['redirect' => '/profile?tab=security&page=2']);
});

it('rejects traversal in next path and falls back to dashboard', function (): void {
    $response = postJson('/dev-override', [
        'token' => 'x',
        'next' => '/../../etc/passwd',
    ]);

    $response->assertOk()->assertJsonPath('redirect', route('dashboard'));
});
