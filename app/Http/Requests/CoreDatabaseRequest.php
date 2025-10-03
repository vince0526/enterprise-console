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
            'environment' => ['nullable', 'string', 'max:255'], // legacy
            'platform' => ['nullable', 'string', 'max:255'], // legacy
            'owner' => ['nullable', 'string', 'max:255'],
            'owner_email' => ['nullable', 'string', 'max:255'],
            'lifecycle' => ['nullable', 'string', 'max:255'],
            'linked_connection' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'max:50'],
            // New fields
            'tier' => ['nullable', 'string', 'max:255'],
            'tax_path' => ['nullable', 'string', 'max:1024'],
            'vc_stage' => ['nullable', 'string', 'max:255'],
            'vc_industry' => ['nullable', 'string', 'max:255'],
            'vc_subindustry' => ['nullable', 'string', 'max:255'],
            'cross_enablers' => ['nullable', 'array'],
            'cross_enablers.*' => ['string', 'max:255'],
            'functional_scopes' => ['nullable', 'array'],
            'functional_scopes.*' => ['string', 'max:255'],
            'engine' => ['nullable', 'string', 'max:255'],
            'env' => ['nullable', 'string', 'max:50'],
        ];
    }
}
