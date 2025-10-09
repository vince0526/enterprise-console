<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoreDatabaseValidationTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): User
    {
        return User::factory()->create();
    }

    public function test_value_chain_requires_stage_industry_subindustry(): void
    {
        $user = $this->actingUser();
        $payload = [
            'name' => 'test_db',
            'tier' => 'Value Chain',
            'engine' => 'PostgreSQL',
            'env' => 'Dev',
            // Missing vc_stage, vc_industry, vc_subindustry
        ];
        $resp = $this->actingAs($user)->post(route('emc.core.store'), $payload);
        $resp->assertSessionHasErrors(['vc_stage', 'vc_industry', 'vc_subindustry']);
    }

    public function test_engine_and_env_required(): void
    {
        $user = $this->actingUser();
        $payload = [
            'name' => 'test_db2',
            'tier' => 'Legacy',
            // engine/env omitted
        ];
        $resp = $this->actingAs($user)->post(route('emc.core.store'), $payload);
        $resp->assertSessionHasErrors(['engine', 'env']);
    }

    public function test_successful_create_with_all_required_fields(): void
    {
        $user = $this->actingUser();
        $payload = [
            'name' => 'vc_sample',
            'tier' => 'Value Chain',
            'vc_stage' => 'Retail & Direct-to-Consumer (Goods)',
            'vc_industry' => 'Retail',
            'vc_subindustry' => 'Grocery',
            'engine' => 'PostgreSQL',
            'env' => 'Dev',
            'functional_scopes' => ['Accounting', 'Inventory'],
        ];
        $resp = $this->actingAs($user)->post(route('emc.core.store'), $payload);
        $resp->assertSessionDoesntHaveErrors();
        $resp->assertRedirect(route('emc.core.index'));
        $this->assertDatabaseHas('core_databases', [
            'name' => 'vc_sample',
            'vc_stage' => 'Retail & Direct-to-Consumer (Goods)',
            'engine' => 'PostgreSQL',
        ]);
    }

    public function test_invalid_engine_and_env_rejected(): void
    {
        $user = $this->actingUser();
        $payload = [
            'name' => 'bad_values',
            'tier' => 'Legacy',
            'engine' => 'MongoDB', // not allowed
            'env' => 'QA', // not allowed
        ];
        $resp = $this->actingAs($user)->post(route('emc.core.store'), $payload);
        $resp->assertSessionHasErrors(['engine', 'env']);
    }
}
