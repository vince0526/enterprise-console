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

    /**
     * Normalize legacy inputs to new fields before validation.
     * - Map platform -> engine
     * - Map environment -> env (Production/Staging/Development -> Prod/UAT/Dev)
     * - Default tier: if not provided, set to 'Value Chain' when tax_path present (wizard), else 'Legacy'
     */
    protected function prepareForValidation(): void
    {
        $data = $this->all();

        // engine fallback from legacy 'platform'
        if (! isset($data['engine']) && isset($data['platform']) && is_string($data['platform'])) {
            $data['engine'] = $data['platform'];
        }

        // env fallback from legacy 'environment'
        if (! isset($data['env']) && isset($data['environment']) && is_string($data['environment'])) {
            $map = [
                'Production' => 'Prod',
                'Staging' => 'UAT',
                'Development' => 'Dev',
            ];
            $data['env'] = $map[$data['environment']] ?? $data['environment'];
        }

        // tier defaulting: prefer explicit; else infer from presence of wizard path
        if (! isset($data['tier']) || $data['tier'] === null || $data['tier'] === '') {
            $data['tier'] = isset($data['tax_path']) && is_string($data['tax_path']) && $data['tax_path'] !== ''
                ? 'Value Chain'
                : 'Legacy';
        }

        $this->replace($data);
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
            // New fields (Value-Chain Workbench)
            'tier' => ['required', 'string', 'max:255'],
            'tax_path' => ['nullable', 'string', 'max:1024'],
            // Stage-first: required only for Value Chain tier
            'vc_stage' => ['required_if:tier,Value Chain', 'nullable', 'string', 'max:255'],
            'vc_industry' => ['required_if:tier,Value Chain', 'nullable', 'string', 'max:255'],
            'vc_subindustry' => ['required_if:tier,Value Chain', 'nullable', 'string', 'max:255'],
            'cross_enablers' => ['nullable', 'array'],
            'cross_enablers.*' => ['string', 'max:255'],
            'functional_scopes' => ['nullable', 'array'],
            'functional_scopes.*' => ['string', 'max:255'],
            // Engine + Env are required for generated records
            'engine' => ['required', 'string', 'max:255'],
            'env' => ['required', 'string', 'max:50'],
        ];
    }
}
