<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * CoreDatabase Model
 *
 * Fields overview:
 * - Legacy: name, environment, platform, owner, lifecycle, linked_connection, description, status
 * - Workbench: tier, tax_path, vc_stage, vc_industry, vc_subindustry, cross_enablers (array),
 *              functional_scopes (array), engine, env, owner_email
 *
 * Notes:
 * - Add/remove mass-assignable fields by updating $fillable. Keep in sync with FormRequest rules.
 * - Array JSON casts (cross_enablers, functional_scopes) are auto-cast to PHP arrays.
 * - Relationships owners/lifecycleEvents/links are used by the UI tabs and registry quickview.
 */
class CoreDatabase extends Model
{
    /** @phpstan-use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\CoreDatabaseFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'name',
        'environment',
        'platform',
        'owner',
        'owner_email',
        'lifecycle',
        'linked_connection',
        'description',
        'status',
        // New fields for Value-Chain module
        'tier',
        'tax_path',
        'vc_stage',
        'vc_industry',
        'vc_subindustry',
        'cross_enablers',
        'functional_scopes',
        'engine',
        'env',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'cross_enablers' => 'array',
        'functional_scopes' => 'array',
    ];

    /**
     * @return HasMany<CoreDatabaseOwner, $this>
     */
    public function owners(): HasMany
    {
        // Ownership submodule (owner_name, role, effective_date)
        return $this->hasMany(CoreDatabaseOwner::class);
    }

    /**
     * @return HasMany<CoreDatabaseLifecycleEvent, $this>
     */
    public function lifecycleEvents(): HasMany
    {
        // Lifecycle submodule (event_type, details, effective_date)
        return $this->hasMany(CoreDatabaseLifecycleEvent::class);
    }

    /**
     * @return HasMany<CoreDatabaseLink, $this>
     */
    public function links(): HasMany
    {
        // Links to database_connections (if present) or external policies/resources
        return $this->hasMany(CoreDatabaseLink::class);
    }

    /**
     * Virtualized legacy environment accessor (maps short env codes back to long form).
     */
    public function getEnvironmentAttribute($value): ?string
    {
        if ($value) {
            return $value;
        } // existing legacy column still populated
        $map = ['Prod' => 'Production', 'UAT' => 'Staging', 'Dev' => 'Development'];

        return $map[$this->attributes['env'] ?? ''] ?? ($this->attributes['env'] ?? null);
    }

    /**
     * Virtualized legacy platform accessor fallback to engine.
     */
    public function getPlatformAttribute($value): ?string
    {
        if ($value) {
            return $value;
        }

        return $this->attributes['engine'] ?? null;
    }
}
