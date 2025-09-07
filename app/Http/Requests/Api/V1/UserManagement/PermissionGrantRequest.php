<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\UserManagement;

use Illuminate\Foundation\Http\FormRequest;

final class PermissionGrantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    /** @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'permissions' => ['required', 'array', 'min:1'],
            'permissions.*' => ['string', 'max:191'],
        ];
    }
}
