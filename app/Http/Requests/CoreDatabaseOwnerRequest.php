<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CoreDatabaseOwnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'core_database_id' => ['required', 'integer', 'exists:core_databases,id'],
            'owner_name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:255'],
            'effective_date' => ['required', 'date'],
        ];
    }
}
