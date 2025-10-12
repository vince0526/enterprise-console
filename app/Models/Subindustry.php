<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subindustry extends Model
{
    use HasFactory;

    protected $fillable = ['industry_id', 'subindustry_name'];

    public function industry()
    {
        return $this->belongsTo(Industry::class);
    }
}
