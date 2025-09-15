<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\DevOverrideUsed;
use App\Listeners\LogDevOverrideUsage;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        DevOverrideUsed::class => [
            LogDevOverrideUsage::class,
        ],
    ];
}
