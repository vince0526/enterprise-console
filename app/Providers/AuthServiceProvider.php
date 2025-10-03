<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\CoreDatabase;
use App\Policies\CoreDatabasePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /** @var array<class-string, class-string> */
    protected $policies = [
        CoreDatabase::class => CoreDatabasePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
