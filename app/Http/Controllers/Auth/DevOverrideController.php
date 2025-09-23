<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Events\DevOverrideUsed;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DevOverrideController extends Controller
{
    public function __invoke(Request $request): \Illuminate\Http\JsonResponse
    {
        $payload = $request->json()->all();
        $token = $payload['token'] ?? $request->input('token');
        // Support optional next (explicit) or intended (session) redirect
        $next = $payload['next'] ?? $request->input('next');

        // Developer token must be set in the configuration for local dev override.
        $expected = (string) config('dev_override.token');
        if ($expected === '') {
            return response()->json(['success' => false, 'message' => 'dev override token not configured'], 422);
        }

        if (! hash_equals($expected, (string) $token)) {
            return response()->json(['success' => false, 'message' => 'invalid token'], 401);
        }

        // Find or create a local developer user
        $email = (string) config('dev_override.email', 'dev@example.com');

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Developer',
                'password' => bcrypt(Str::random(32)),
            ]
        );

        Auth::login($user);

        // Dispatch synchronously (and via helper) so Event::fake can track it.
        DevOverrideUsed::dispatch($user->getKey(), (string) $user->email, $request->ip());
        Log::info('Dev override used', ['user_id' => $user->getKey(), 'email' => $user->email]);

        // Determine safe redirect target
        $redirect = route('dashboard');
        if (is_string($next) && $next !== '') {
            // Allow only relative paths (leading '/') optionally with query/fragment.
            // Use parse_url for safer validation instead of complex regex.
            if (str_starts_with($next, '/') && ! str_contains($next, '..')) {
                $parts = parse_url($next);
                // path is always present if string starts with '/'
                $path = $parts['path'] ?? '/';
                $query = isset($parts['query']) ? '?'.$parts['query'] : '';
                $fragment = isset($parts['fragment']) ? '#'.$parts['fragment'] : '';
                // Basic whitelist: path only contains safe chars
                if (preg_match('#^/[A-Za-z0-9_\-/\.]*$#', $path) === 1) {
                    $redirect = $path.$query.$fragment;
                }
            }
        } elseif ($request->hasSession()) {
            // Fallback to intended URL if present in the session
            $intended = $request->session()->pull('url.intended');
            if (is_string($intended) && str_starts_with($intended, '/')) {
                $redirect = $intended;
            }
        }

        return response()->json(['success' => true, 'redirect' => $redirect]);
    }
}
