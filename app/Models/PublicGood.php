<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\PublicGoodFactory>
 *
 * @phpstan-use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\PublicGoodFactory>
 *
 * @mixin \Eloquent
 */
class PublicGood extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
}
