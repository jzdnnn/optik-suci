<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LensType extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function lenses()
    {
        return $this->hasMany(Lens::class);
    }
}
