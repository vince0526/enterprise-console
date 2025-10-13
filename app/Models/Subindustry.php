<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\SubindustryFactory>
 *
 * @phpstan-use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\SubindustryFactory>
 *
 * @mixin \Eloquent
 */
class Subindustry extends Model
{
    use HasFactory;

    /**
     * @return BelongsTo<\App\Models\Industry, \App\Models\Subindustry>
     */
    public function industry(): BelongsTo
    {
        /** @var BelongsTo<\App\Models\Industry, \App\Models\Subindustry> $relation */
        $relation = $this->belongsTo(Industry::class);

        return $relation;
    }
}
