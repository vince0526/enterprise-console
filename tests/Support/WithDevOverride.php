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
}
