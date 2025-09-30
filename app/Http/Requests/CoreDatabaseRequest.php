<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CoreDatabaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // gate later if needed
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'environment' => ['required', 'string', 'max:255'],
            'platform' => ['required', 'string', 'max:255'],
            'owner' => ['nullable', 'string', 'max:255'],
            'lifecycle' => ['nullable', 'string', 'max:255'],
            'linked_connection' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'max:50'],
        ];
    }
}
