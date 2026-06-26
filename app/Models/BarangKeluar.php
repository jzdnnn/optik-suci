<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy(\App\Observers\BarangKeluarObserver::class)]
class BarangKeluar extends Model
{
    protected $table = 'barang_keluar';
    protected $guarded = [];

    protected $casts = [
        'aksesoris' => 'array',
        'tanggal_transaksi' => 'date',
        'tanggal_pengambilan' => 'date',
        'tanggal_pelunasan' => 'date',
        'jumlah_lensa_pcs' => 'integer',
        'diskon' => 'decimal:2',
        'potongan_bpjs' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::saving(function ($model) {
            if ($model->status_pembayaran === 'lunas' && is_null($model->tanggal_pelunasan)) {
                $model->tanggal_pelunasan = $model->tanggal_transaksi ?? now();
            }
        });
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function frame()
    {
        return $this->belongsTo(Frame::class);
    }

    public function lens()
    {
        return $this->belongsTo(Lens::class);
    }
}
