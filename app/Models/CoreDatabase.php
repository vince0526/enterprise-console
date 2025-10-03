<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        return $this->hasMany(CoreDatabaseOwner::class);
    }

    /**
     * @return HasMany<CoreDatabaseLifecycleEvent, $this>
     */
    public function lifecycleEvents(): HasMany
    {
        return $this->hasMany(CoreDatabaseLifecycleEvent::class);
    }

    /**
     * @return HasMany<CoreDatabaseLink, $this>
     */
    public function links(): HasMany
    {
        return $this->hasMany(CoreDatabaseLink::class);
    }
}
