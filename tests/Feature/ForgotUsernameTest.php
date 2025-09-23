<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ForgotUsernameTest extends TestCase
{
    public function test_send_and_verify_username_recovery()
    {
        Mail::fake();
        Cache::flush();

        // ensure migrations ran
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);

        $user = User::factory()->create(['email' => 'foo@example.test', 'name' => 'foo']);

        $resp = $this->postJson(route('auth.recover-username'), ['email' => $user->email]);
        $resp->assertStatus(200)->assertJson(['success' => true]);

        // read cached code
        $key = 'username_recovery_'.sha1($user->email);
        $data = Cache::get($key);
        $this->assertNotEmpty($data['code']);

        $verify = $this->postJson(route('auth.verify-username-code'), ['email' => $user->email, 'code' => $data['code']]);
        $verify->assertStatus(200)->assertJson(['success' => true, 'username' => $user->name]);
    }
}
