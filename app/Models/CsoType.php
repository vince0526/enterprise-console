<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @phpstan-use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\CsoTypeFactory>
 */
class CsoType extends Model
{
    use HasFactory;

    protected $fillable = ['cso_super_category_id', 'name'];
}
