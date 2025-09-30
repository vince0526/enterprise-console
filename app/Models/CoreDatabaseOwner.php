<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoreDatabaseOwner extends Model
{
    protected $fillable = [
        'core_database_id',
        'owner_name',
        'role',
        'effective_date',
    ];

    /**
     * @return BelongsTo<CoreDatabase, self>
     */
    public function coreDatabase(): BelongsTo
    {
        return $this->belongsTo(CoreDatabase::class);
    }
}
