<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CoreDatabase extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'name',
        'environment',
        'platform',
        'owner',
        'lifecycle',
        'linked_connection',
        'description',
        'status',
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
