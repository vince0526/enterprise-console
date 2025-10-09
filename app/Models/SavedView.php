<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * SavedView model
 *
 * Represents a persisted saved view (filter preset) for the Core Databases registry.
 * Columns: id, user_id, name, context, filters (json), created_at, updated_at
 * - context: short string namespace (e.g. "core_databases") to allow reuse for future modules.
 * - filters: JSON object representing query parameter key/value pairs or arrays.
 */
class SavedView extends Model
{
    /** @phpstan-use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\SavedViewFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'name',
        'context',
        'filters',
    ];

    /** @var array<string,string> */
    protected $casts = [
        'filters' => 'array',
    ];
}
