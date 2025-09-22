<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DevOverrideStatus extends Command
{
    protected $signature = 'dev-override:status';

    protected $description = 'Show current dev override configuration status';

    public function handle(): int
    {
        $enabled = (bool) config('dev_override.enabled');
        $tokenSet = config('dev_override.token') !== null && config('dev_override.token') !== '';
        $email = (string) config('dev_override.email');
        $env = app()->environment();

        $this->line('Environment: <info>'.$env.'</info>');
        $this->line('Enabled flag: '.($enabled ? '<info>true</info>' : '<comment>false</comment>'));
        $this->line('Token present: '.($tokenSet ? '<info>yes</info>' : '<error>no</error>'));
        $this->line('Email: <info>'.$email.'</info>');

        if ($env === 'production') {
            $this->warn('Dev override is forcibly disabled in production.');
        } elseif (! $enabled) {
            $this->warn('Set DEV_OVERRIDE_ENABLED=true to enable.');
        } elseif (! $tokenSet) {
            $this->warn('Set DEV_OVERRIDE_TOKEN=your-token-value in .env then run config:clear.');
        } else {
            $this->info('Dev override endpoint should be available.');
        }

        return 0;
    }
}
