<?php

declare(strict_types=1);

use Illuminate\Testing\TestResponse;

/**
 * EMC endpoints and core assets should be reachable in non-production env.
 */
dataset('emc_endpoints', function () {
    return [
        '/emc',
        '/emc/db',
        '/emc/tables',
        '/emc/files',
        '/emc/users',
        '/emc/reports',
        '/emc/ai',
        '/emc/comms',
        '/emc/settings',
        '/emc/activity',
        '/emc/about',
    ];
});

it('responds OK for EMC sweep endpoints', function (string $path) {
    $response = $this->get($path);

    // Allow basic redirects to 200 destinations (e.g., if front controller redirects)
    if (in_array($response->getStatusCode(), [301, 302, 303, 307, 308], true)) {
        /** @var TestResponse $followed */
        $followed = $this->followingRedirects()->get($path);
        $followed->assertOk();
    } else {
        $response->assertOk();
    }
})->with('emc_endpoints');

it('has core static assets available on disk', function () {
    $css = public_path('css/emc.css');
    $js = public_path('js/emc.js');
    expect(is_file($css))->toBeTrue();
    expect(is_file($js))->toBeTrue();
    expect(filesize($css))->toBeGreaterThan(1000);
    expect(filesize($js))->toBeGreaterThan(500);
});
