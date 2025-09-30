<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoreDatabaseLink extends Model
{
    protected $fillable = [
        'core_database_id',
        'database_connection_id',
        'linked_connection_name',
        'link_type',
    ];

    /**
     * @return BelongsTo<CoreDatabase, $this>
     */
    public function coreDatabase(): BelongsTo
    {
        return $this->belongsTo(CoreDatabase::class);
    }

    /**
     * @return BelongsTo<DatabaseConnection, $this>
     */
    public function databaseConnection(): BelongsTo
    {
        return $this->belongsTo(DatabaseConnection::class);
    }
}
