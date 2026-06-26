<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisPengeluaran extends Model
{
    protected $table = 'jenis_pengeluaran';
    protected $guarded = [];

    public function pengeluaran()
    {
        return $this->hasMany(Pengeluaran::class);
    }
}
