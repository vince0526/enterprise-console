<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DevOverrideController extends Controller
{
    public function __invoke(Request $request): \Illuminate\Http\JsonResponse
    {
        // Only allow on non-production by default
        if (app()->environment('production')) {
            return response()->json(['success' => false, 'message' => 'Not allowed in production'], 403);
        }

        $payload = $request->json()->all();
        $token = $payload['token'] ?? $request->input('token');

        // Developer token must be set in the configuration for local dev override.
        $expected = (string) config('dev_override.token');
        if ($expected === '') {
            return response()->json(['success' => false, 'message' => 'dev override token not configured'], 500);
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

        return response()->json(['success' => true, 'redirect' => route('dashboard')]);
    }
}
