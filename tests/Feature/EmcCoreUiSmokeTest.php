<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmcCoreUiSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_engine_and_env_filter_selects_present(): void
    {
        /** @var User&\Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create();
        $resp = $this->actingAs($user)->get(route('emc.core.index', ['tab' => 'registry']));
        $resp->assertOk();
        $html = $resp->getContent();
        $this->assertStringContainsString('name="engine"', $html);
        $this->assertStringContainsString('name="env"', $html);
        $this->assertStringContainsString('<option value="PostgreSQL"', $html);
        $this->assertStringContainsString('<option value="Dev"', $html);
    }
}
