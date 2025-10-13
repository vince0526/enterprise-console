<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\CsoSuperCategoryFactory>
 *
 * @phpstan-use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\CsoSuperCategoryFactory>
 *
 * @mixin \Eloquent
 */
class CsoSuperCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
}
