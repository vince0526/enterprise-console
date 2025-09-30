<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use App\Models\CoreDatabase;
use App\Models\CoreDatabaseOwner;
use App\Models\CoreDatabaseLifecycleEvent;
use App\Models\CoreDatabaseLink;

uses(RefreshDatabase::class);

it('seeds core submodule data', function () {
    Artisan::call('db:seed', ['--class' => \Database\Seeders\DatabaseSeeder::class]);

    expect(CoreDatabase::count())->toBeGreaterThan(0);
    expect(CoreDatabaseOwner::count())->toBeGreaterThan(0);
    expect(CoreDatabaseLifecycleEvent::count())->toBeGreaterThan(0);
    expect(CoreDatabaseLink::count())->toBeGreaterThan(0);
});
