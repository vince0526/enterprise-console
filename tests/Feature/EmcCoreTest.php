<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects root to /emc/core', function () {
    $response = $this->get('/');
    $response->assertRedirect('/emc/core');
});

it('redirects /emc to /emc/core', function () {
    $response = $this->get('/emc');
    $response->assertRedirect('/emc/core');
});

it('has core routes', function () {
    expect(route('emc.core.index'))->toBe(url('/emc/core'));
});

it('can create a core database record', function () {
    $payload = [
        'name' => 'Test DB',
        'environment' => 'Production',
        'platform' => 'MySQL',
    ];

    $response = $this->post('/emc/core', $payload);

    $response->assertStatus(302);
    $this->assertDatabaseHas('core_databases', [
        'name' => 'Test DB',
        'environment' => 'Production',
        'platform' => 'MySQL',
    ]);
});
