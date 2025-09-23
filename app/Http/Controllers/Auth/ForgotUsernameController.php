<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\UsernameRecoveryMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class ForgotUsernameController extends Controller
{
    public function send(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (! $user) {
            // don't reveal whether email exists
            return response()->json(['success' => true]);
        }

        $code = rand(100000, 999999);
        $key = 'username_recovery_'.sha1($request->email);
        Cache::put($key, ['code' => $code, 'email' => $request->email], now()->addMinutes(15));

        Mail::to($request->email)->send(new UsernameRecoveryMail($code, $user->name));

        return response()->json(['success' => true]);
    }

    public function verify(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate(['email' => 'required|email', 'code' => 'required']);

        $key = 'username_recovery_'.sha1($request->email);
        $data = Cache::get($key);
        if (! $data || (string) $data['code'] !== (string) $request->code) {
            return response()->json(['success' => false, 'message' => 'Invalid code'], 422);
        }

        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        // optionally invalidate
        Cache::forget($key);

        return response()->json(['success' => true, 'username' => $user->name]);
    }
}
