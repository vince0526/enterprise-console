<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Development helper: automatically logs in a configured user so the UI can be viewed
 * without going through the auth flow. Guarded by DEV_AUTO_LOGIN env flag and never
 * active in production. Disable by setting DEV_AUTO_LOGIN=false (or removing) in .env.
 */
class DevAutoLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        $active = $this->shouldAutoLogin();
        if ($active) {
            Log::debug('DevAutoLogin: middleware active for request', [
                'path' => $request->path(),
                'already_auth' => Auth::check(),
            ]);
            $this->ensureLoggedIn();
            // If user hits /login while dev auto login is enabled, push them to dashboard.
            if ($request->is('login')) {
                return redirect()->route('dashboard')->with('dev-auto-login', true);
            }
        } else {
            Log::debug('DevAutoLogin: skipped (flag off or production)');
        }

        /** @var Response $response */
        $response = $next($request);
        if ($active && method_exists($response, 'headers')) {
            $response->headers->set('X-Dev-Auto-Login', '1');
        }

        return $response;
    }

    protected function shouldAutoLogin(): bool
    {
        if (app()->environment('production')) {
            return false; // safety: never in production
        }

        return (bool) config('app.dev_auto_login', false);
    }

    protected function ensureLoggedIn(): void
    {
        if (Auth::check()) {
            Log::debug('DevAutoLogin: user already authenticated', ['id' => Auth::id()]);

            return; // already logged in
        }

        $userId = (int) config('app.dev_auto_login_user_id', 1);
        $attempts = [];
        $user = null;

        // Strategy 1: Direct Eloquent lookup
        try {
            $user = User::query()->find($userId);
            $attempts[] = ['strategy' => 'configured_id', 'found' => (bool) $user];
        } catch (\Throwable $e) {
            Log::warning('DevAutoLogin: error querying configured id', ['error' => $e->getMessage()]);
        }

        // Strategy 2: first user
        if (! $user) {
            try {
                $user = User::query()->orderBy('id')->first();
                $attempts[] = ['strategy' => 'first_user', 'found' => (bool) $user];
            } catch (\Throwable $e) {
                Log::warning('DevAutoLogin: error querying first user', ['error' => $e->getMessage()]);
            }
        }

        // Strategy 3: create temp user if table empty
        if (! $user) {
            try {
                if (User::query()->count() === 0) {
                    $user = User::query()->create([
                        'name' => 'Dev User',
                        'email' => 'dev@example.test',
                        'password' => bcrypt('password'),
                        'email_verified_at' => now(),
                    ]);
                    $attempts[] = ['strategy' => 'create_temp', 'found' => (bool) $user];
                }
            } catch (\Throwable $e) {
                Log::warning('DevAutoLogin: error creating temp user', ['error' => $e->getMessage()]);
            }
        }

        Log::debug('DevAutoLogin: strategies summary', ['attempts' => $attempts]);

        if ($user) {
            Auth::guard('web')->login($user);
            Log::info('DevAutoLogin: auto-logged in user', ['id' => $user->getKey()]);
        } else {
            Log::warning('DevAutoLogin: no user available after strategies');
        }
    }
}
