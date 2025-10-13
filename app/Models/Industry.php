<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Industry extends Model
{
    use HasFactory;

    protected $fillable = ['industry_name', 'notes'];

    /**
     * @phpstan-return HasMany<\App\Models\Subindustry, \App\Models\Industry>
     */
    public function subindustries(): HasMany
    {
        return $this->hasMany(Subindustry::class);
    }
}
