<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @phpstan-use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\CsoSuperCategoryFactory>
 */
class CsoSuperCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
}
