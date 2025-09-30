<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CoreDatabaseLinkRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'core_database_id' => ['required', 'integer', 'exists:core_databases,id'],
            'database_connection_id' => ['nullable', 'integer', 'exists:database_connections,id'],
            'linked_connection_name' => ['required', 'string', 'max:255'],
            'link_type' => ['required', 'string', 'max:255'],
        ];
    }
}
