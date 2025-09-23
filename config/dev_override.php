<?php

declare(strict_types=1);

return [
    'enabled' => env('DEV_OVERRIDE_ENABLED', false),
    'token' => env('DEV_OVERRIDE_TOKEN', ''),
    'email' => env('DEV_OVERRIDE_EMAIL', 'dev@example.com'),
];
