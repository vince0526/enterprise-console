<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Normalize inputs before validation.
     */
    protected function prepareForValidation(): void
    {
        // Support legacy 'email' input and new 'identifier' input.
        $identifier = $this->input('identifier', $this->input('email', ''));
        $identifier = is_scalar($identifier) ? (string) $identifier : '';

        // normalize
        $identifier = trim($identifier);

        // if marked as encoded, attempt base64 decode
        if ($this->boolean('use_encoded')) {
            $decoded = @base64_decode($identifier, true);
            if (is_string($decoded) && $decoded !== '') {
                $identifier = $decoded;
            }
        }

        $this->merge([
            'identifier' => $identifier,
        ]);
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Determine credential field. If input looks like an email, try 'email', else try 'name' or 'email'.
        $identifier = $this->input('identifier', '');
        $credentials = ['password' => $this->input('password', '')];

        $attempted = false;

        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $credentials['email'] = (string) $identifier;
            $attempted = Auth::attempt($credentials, $this->boolean('remember'));
        } else {
            // try username (name) first, then email
            $credentials['name'] = (string) $identifier;
            $attempted = Auth::attempt($credentials, $this->boolean('remember'));

            if (! $attempted) {
                unset($credentials['name']);
                $credentials['email'] = (string) $identifier;
                $attempted = Auth::attempt($credentials, $this->boolean('remember'));
            }
        }

        if (! $attempted) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'identifier' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        $idRaw = $this->input('identifier', '');
        $id = is_scalar($idRaw) ? (string) $idRaw : '';

        $ip = $this->ip();
        $ip = is_scalar($ip) ? (string) $ip : '';

        $key = $id.'|'.$ip;

        return (string) Str::transliterate($key);
    }
}
