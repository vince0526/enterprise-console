<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @phpstan-use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\ProgramFactory>
 */
class Program extends Model
{
    use HasFactory;

    protected $fillable = ['pg_id', 'lead_org_id', 'delivery_mode', 'benefit_type', 'status'];
}
