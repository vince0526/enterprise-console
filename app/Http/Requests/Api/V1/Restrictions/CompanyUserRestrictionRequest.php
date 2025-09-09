<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Restrictions;

use Illuminate\Foundation\Http\FormRequest;

final class CompanyUserRestrictionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage', \App\Models\CompanyUserRestriction::class) ?? false;
    }

    /** @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'database_connection_id' => ['required', 'integer', 'exists:database_connections,id'],
            'read_only' => ['sometimes', 'boolean'],
        ];
    }

    /** @return array<string, mixed> */
    public function validated($key = null, $default = null): array
    {
        /** @var array<string, mixed> $data */
        $data = parent::validated($key, $default) ?? [];

        if (isset($data['read_only'])) {
            $data['read_only'] = (bool) $data['read_only'];
        }

        return $data;
    }
}
