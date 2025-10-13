<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @phpstan-use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\GovOrgFactory>
 *
 * @mixin \Eloquent
 */
class GovOrg extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'org_type', 'jurisdiction', 'is_soe', 'parent_org_id'];
}
