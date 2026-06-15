<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FrameTransaction extends Model
{
    protected $guarded = [];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function frame()
    {
        return $this->belongsTo(Frame::class);
    }
}
