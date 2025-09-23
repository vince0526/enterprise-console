<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OAuthController extends Controller
{
    /**
     * @param  non-empty-string  $provider
     */
    public function redirect(Request $request, string $provider): \Symfony\Component\HttpFoundation\Response
    {
        // soft-degrade if Socialite not installed
        if (! class_exists(\Laravel\Socialite\Facades\Socialite::class)) {
            return response()->json([
                'message' => 'OAuth not configured. Install laravel/socialite and add client id/secret in .env',
            ], 501);
        }

        return \Laravel\Socialite\Facades\Socialite::driver($provider)->redirect();
    }

    /**
     * @param  non-empty-string  $provider
     */
    public function callback(Request $request, string $provider): \Symfony\Component\HttpFoundation\Response
    {
        if (! class_exists(\Laravel\Socialite\Facades\Socialite::class)) {
            return response()->json(['message' => 'OAuth not configured'], 501);
        }

        // use stateless to avoid session issues for API-style flows
        try {
            $driver = \Laravel\Socialite\Facades\Socialite::driver($provider);
            // Some providers expose stateless() only on concrete driver; ignore if unavailable
            if (method_exists($driver, 'stateless')) {
                /** @var object{stateless: callable(): mixed} $driver */
                $driver = $driver->stateless();
            }
            /** @var \Laravel\Socialite\Contracts\User $socialUser */
            $socialUser = $driver->user();
        } catch (\Exception $e) {
            return response()->json(['message' => 'OAuth provider error', 'error' => $e->getMessage()], 502);
        }

        $email = $socialUser->getEmail();
        $name = $socialUser->getName() ?: $socialUser->getNickname() ?: Str::before($email, '@');

        if (! $email) {
            return response()->json(['message' => 'OAuth provider did not return an email'], 422);
        }

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => bcrypt(Str::random(40)),
            ]
        );

        Auth::login($user);

        // if request expects JSON, return JSON; otherwise redirect to dashboard
        if ($request->wantsJson() || $request->isXmlHttpRequest()) {
            return response()->json(['success' => true, 'redirect' => route('dashboard')]);
        }

        return redirect()->intended(route('dashboard'));
    }
}
