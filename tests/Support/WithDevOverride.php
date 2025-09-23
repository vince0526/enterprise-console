<?php

declare(strict_types=1);

namespace Tests\Support;

trait WithDevOverride
{
    protected function enableDevOverride(string $token = 'test-dev-token'): void
    {
        putenv('DEV_OVERRIDE_TOKEN='.$token);
        $_ENV['DEV_OVERRIDE_TOKEN'] = $token;
        config([
            'dev_override.enabled' => true,
            'dev_override.token' => $token,
        ]);
    }

    protected function enableDevOverrideFlagOnly(): void
    {
        // Explicitly clear token env and config to simulate missing token scenario.
        putenv('DEV_OVERRIDE_TOKEN'); // unsets value
        unset($_ENV['DEV_OVERRIDE_TOKEN']);
        config([
            'dev_override.enabled' => true,
            'dev_override.token' => '',
        ]);
    }
}
