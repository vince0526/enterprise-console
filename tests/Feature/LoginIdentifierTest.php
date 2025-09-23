<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class LoginIdentifierTest extends TestCase
{
    public function test_login_with_email_identifier()
    {
        Artisan::call('migrate', ['--force' => true]);

        $password = 'password123';
        $user = User::factory()->create(['email' => 'alice@example.test', 'name' => 'alice', 'password' => bcrypt($password)]);

        $resp = $this->post('/login', ['identifier' => $user->email, 'password' => $password]);
        $resp->assertRedirect('/dashboard');
    }

    public function test_login_with_username_identifier()
    {
        Artisan::call('migrate', ['--force' => true]);

        $password = 'password123';
        $user = User::factory()->create(['email' => 'bob@example.test', 'name' => 'bob', 'password' => bcrypt($password)]);

        $resp = $this->post('/login', ['identifier' => $user->name, 'password' => $password]);
        $resp->assertRedirect('/dashboard');
    }

    public function test_login_with_encoded_identifier()
    {
        Artisan::call('migrate', ['--force' => true]);

        $password = 'password123';
        $user = User::factory()->create(['email' => 'carol@example.test', 'name' => 'carol', 'password' => bcrypt($password)]);

        $encoded = base64_encode($user->name);

        $resp = $this->post('/login', ['identifier' => $encoded, 'use_encoded' => '1', 'password' => $password]);
        $resp->assertRedirect('/dashboard');
    }
}
