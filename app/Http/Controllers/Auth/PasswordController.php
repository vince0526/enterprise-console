<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        /** @var array<string, mixed> $validated */
        /** @var \App\Models\User|null $user */
        $user = $request->user();

        if (isset($validated['password'])) {
            $pwRaw = $validated['password'];
            $pw = is_scalar($pwRaw) ? (string) $pwRaw : '';

            $user?->update([
                'password' => Hash::make($pw),
            ]);
        }

        return back()->with('status', 'password-updated');
    }
}
