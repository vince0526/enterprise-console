<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Http\Controllers\Auth\DevOverrideController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Tests\Support\WithDevOverride;
use Tests\TestCase;

class DevOverrideDisabledTest extends TestCase
{
    use WithDevOverride;

    public function test_dev_override_disabled_blocks_access(): void
    {
        Artisan::call('migrate', ['--force' => true]);

        // Start from enabled baseline then disable to ensure path works from either state.
        $this->enableDevOverride('test-token');
        config(['dev_override.enabled' => false]);

        $req = Request::create('/dev-override', 'POST', [], [], [], [], json_encode(['token' => 'test-token']));
        $controller = new DevOverrideController;
        $resp = $controller($req);

        $this->assertEquals(403, $resp->getStatusCode());
        $data = $resp->getData(true);
        $this->assertFalse($data['success']);
        $this->assertEquals('dev override disabled', $data['message']);
    }
}
