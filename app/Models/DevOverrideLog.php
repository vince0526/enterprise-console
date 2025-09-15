<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @phpstan-use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\DevOverrideLogFactory>
 */
class DevOverrideLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email',
        'ip',
    ];
}
