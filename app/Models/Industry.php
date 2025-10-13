<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @phpstan-use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\IndustryFactory>
 */
class Industry extends Model
{
    use HasFactory;

    protected $fillable = ['industry_name', 'notes'];

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
