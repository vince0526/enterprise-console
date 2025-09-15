<?php

declare(strict_types=1);
require __DIR__.'/../vendor/autoload.php';

use App\Http\Controllers\Auth\DevOverrideController;
use Illuminate\Http\Request;

$app = require __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$token = $argv[1] ?? getenv('DEV_OVERRIDE_TOKEN') ?? null;
if (! $token) {
    fwrite(STDERR, "Usage: php run_dev_override_test.php <token> or set DEV_OVERRIDE_TOKEN in env\n");
    exit(1);
}
$req = Request::create('/dev-override', 'POST', [], [], [], [], json_encode(['token' => $token]));
$controller = new DevOverrideController;
$response = $controller($req);

echo json_encode($response->getData(true));
