<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Config;

use function Pest\Laravel\postJson;

it('redirects to provided next path when valid', function (): void {
    Config::set('dev_override.enabled', true);
    Config::set('dev_override.token', 'x');
    Config::set('dev_override.email', 'dev@example.test');

    $response = postJson('/dev-override', [
        'token' => 'x',
        'next' => '/profile',
    ]);

    $response->assertOk()->assertJson(['redirect' => '/profile']);
});

it('falls back to dashboard when next is unsafe absolute url', function (): void {
    Config::set('dev_override.enabled', true);
    Config::set('dev_override.token', 'x');
    Config::set('dev_override.email', 'dev@example.test');

    $response = postJson('/dev-override', [
        'token' => 'x',
        'next' => 'https://evil.example/phish',
    ]);

    $response->assertOk()->assertJson(['redirect' => route('dashboard')]);
});
