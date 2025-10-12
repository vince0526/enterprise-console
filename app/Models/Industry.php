<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
    use HasFactory;

    protected $fillable = ['industry_name', 'notes'];

    public function subindustries()
    {
        return $this->hasMany(Subindustry::class);
    }
}
