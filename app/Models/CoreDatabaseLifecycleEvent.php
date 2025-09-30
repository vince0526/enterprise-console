<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoreDatabaseLifecycleEvent extends Model
{
    protected $fillable = [
        'core_database_id',
        'event_type',
        'details',
        'effective_date',
    ];

    protected $casts = [
        'effective_date' => 'date',
    ];

    /**
     * @return BelongsTo<CoreDatabase, $this>
     */
    public function coreDatabase(): BelongsTo
    {
        return $this->belongsTo(CoreDatabase::class);
    }
}
