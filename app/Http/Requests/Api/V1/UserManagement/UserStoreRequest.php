<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\UserManagement;

use Illuminate\Foundation\Http\FormRequest;

final class UserStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\User::class) ?? false;
    }

    /** @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ];
    }

    /**
     * Return validated input with stable string types for consumers.
     *
     * @return array<string, string>
     */
    public function validated($key = null, $default = null): array
    {
        /** @var array<string, mixed> $data */
        $data = parent::validated($key, $default) ?? [];

        $nameRaw = $data['name'] ?? '';
        $emailRaw = $data['email'] ?? '';
        $passwordRaw = $data['password'] ?? '';

        $name = is_scalar($nameRaw) ? (string) $nameRaw : '';
        $email = is_scalar($emailRaw) ? (string) $emailRaw : '';
        $password = is_scalar($passwordRaw) ? (string) $passwordRaw : '';

        return [
            'name' => $name,
            'email' => \Illuminate\Support\Str::lower(trim($email)),
            'password' => $password,
        ];
    }
}
