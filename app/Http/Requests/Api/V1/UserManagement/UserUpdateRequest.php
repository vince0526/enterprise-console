<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\UserManagement;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property-read \App\Models\User|null $user
 */
class UserUpdateRequest extends FormRequest
{
    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var \App\Models\User|null $user */
        $user = $this->route('user');

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user?->id),
            ],
            'password' => ['sometimes', 'string', 'min:8'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function validated($key = null, $default = null): array
    {
        /** @var array<string, mixed> $data */
        $data = parent::validated($key, $default) ?? [];

        $out = [];

        if (array_key_exists('name', $data)) {
            $out['name'] = is_scalar($data['name']) ? (string) $data['name'] : '';
        }

        if (array_key_exists('email', $data)) {
            $email = is_scalar($data['email']) ? (string) $data['email'] : '';
            $out['email'] = \Illuminate\Support\Str::lower(trim($email));
        }

        if (array_key_exists('password', $data)) {
            $out['password'] = is_scalar($data['password']) ? (string) $data['password'] : '';
        }

        return $out;
    }

    public function authorize(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = $this->route('user');

        return $this->user()?->can('update', $user) ?? false;
    }
}
