<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subindustry extends Model
{
    use HasFactory;

    protected $fillable = ['industry_id', 'subindustry_name'];

    /**
     * @phpstan-return BelongsTo<\App\Models\Industry, \App\Models\Subindustry>
     */
    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class);
    }
}
