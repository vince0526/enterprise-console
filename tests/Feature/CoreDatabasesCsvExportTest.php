<?php

declare(strict_types=1);

use App\Models\CoreDatabase;
use App\Models\User;
use Spatie\Permission\Models\Permission;

it('exports registry csv', function () {
    // authorize
    $user = User::factory()->create();
    Permission::findOrCreate('core.view');
    $user->givePermissionTo('core.view');
    $this->actingAs($user);
    CoreDatabase::factory()->create(['name' => 'Alpha DB', 'engine' => 'PostgreSQL', 'env' => 'Dev']);

    $resp = $this->get('/emc/core/export/csv');
    $resp->assertOk();
    $resp->assertHeader('content-type', 'text/csv; charset=UTF-8');
    expect($resp->getContent())->toContain('id,name,engine,env,tier,tax_path,owner,status,updated_at');
});
