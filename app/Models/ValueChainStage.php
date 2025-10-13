<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\ValueChainStageFactory>
 *
 * @phpstan-use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\ValueChainStageFactory>
 *
 * @mixin \Eloquent
 */
class ValueChainStage extends Model
{
    use HasFactory;

    protected $fillable = ['stage_name', 'description'];
}
