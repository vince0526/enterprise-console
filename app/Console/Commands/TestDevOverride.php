<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Http\Controllers\Auth\DevOverrideController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class TestDevOverride extends Command
{
    protected $signature = 'test:dev-override {token}';

    protected $description = 'Run DevOverrideController logic with given token (local testing)';

    public function handle(): int
    {
        $token = (string) $this->argument('token');
        $req = Request::create('/dev-override', 'POST', [], [], [], [], (string) json_encode(['token' => $token]));
        $controller = new DevOverrideController; // style: no parentheses for instantiation

        $response = $controller($req);

        $this->info((string) json_encode($response->getData(true)));

        return 0; // Laravel treats 0 as success exit code
    }
}
