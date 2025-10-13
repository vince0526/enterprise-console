<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\IndustryFactory>
 *
 * @phpstan-use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\IndustryFactory>
 *
 * @mixin \Eloquent
 */
class Industry extends Model
{
    use HasFactory;

    /**
     * @return HasMany<\App\Models\Subindustry, \App\Models\Industry>
     */
    public function subindustries(): HasMany
    {
        /** @var HasMany<\App\Models\Subindustry, \App\Models\Industry> $relation */
        $relation = $this->hasMany(Subindustry::class);

        return $relation;
    }
}
