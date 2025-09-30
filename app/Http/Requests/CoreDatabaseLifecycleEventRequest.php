<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CoreDatabaseLifecycleEventRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'core_database_id' => ['required', 'integer', 'exists:core_databases,id'],
            'event_type' => ['required', 'string', 'max:255'],
            'details' => ['nullable', 'string'],
            'effective_date' => ['required', 'date'],
        ];
    }
}
