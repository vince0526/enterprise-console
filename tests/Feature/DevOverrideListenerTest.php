<?php

declare(strict_types=1);

use App\Models\DevOverrideLog;
use Illuminate\Support\Facades\Config;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

it('creates a dev override log entry when hitting the endpoint', function (): void {
    // Arrange
    Config::set('dev_override.enabled', true);
    Config::set('dev_override.token', 'secret-token');
    Config::set('dev_override.email', 'dev@example.test');

    // Act
    $response = postJson('/dev-override', [
        'token' => 'secret-token',
    ]);

    // Assert response first
    $response->assertOk();

    assertDatabaseCount('dev_override_logs', 1);
    assertDatabaseHas('dev_override_logs', [
        'email' => 'dev@example.test',
    ]);

    $log = DevOverrideLog::first();
    expect($log)->not()->toBeNull();
    expect($log->ip)->not()->toBeEmpty();
});
