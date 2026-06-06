<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lens extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = ['accessories' => 'array'];
    public function lensCategory() { return $this->belongsTo(LensCategory::class); }
}
