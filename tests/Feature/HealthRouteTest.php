<?php

declare(strict_types=1);

use Illuminate\Testing\TestResponse;

it('responds to GET /health with 200 and JSON body', function () {
    /** @var TestResponse $res */
    $res = $this->get('/health');
    $res->assertOk();
    $res->assertJsonStructure([
        'status',
        'app',
        'env',
        'time',
    ]);
});
