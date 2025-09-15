<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Http\Controllers\Auth\DevOverrideController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Tests\Support\WithDevOverride;
use Tests\TestCase;

class DevOverrideTest extends TestCase
{
    use WithDevOverride;

    public function test_dev_override_creates_user_and_returns_success()
    {
        // run migrations to ensure users table exists
        Artisan::call('migrate', ['--force' => true]);

        // ensure the env token is set for the test
        // Use a non-sensitive test token set in the test environment.
        $this->enableDevOverride();
        $token = 'test-dev-token';
        $req = Request::create('/dev-override', 'POST', [], [], [], [], json_encode(['token' => $token]));
        $controller = new DevOverrideController;

        $resp = $controller($req);

        $this->assertEquals(200, $resp->getStatusCode());
        $data = $resp->getData(true);
        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('redirect', $data);
        $this->assertIsString($data['redirect']);
        $this->assertNotEmpty($data['redirect']);
    }
}
