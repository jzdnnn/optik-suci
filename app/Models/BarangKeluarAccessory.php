<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangKeluarAccessory extends Model
{
    use HasFactory;

    protected $table = 'barang_keluar_accessories';
    protected $guarded = [];

    protected $casts = [
        'qty' => 'integer',
        'harga_jual_satuan' => 'decimal:2',
        'subtotal_jual' => 'decimal:2',
        'harga_beli_satuan' => 'decimal:2',
        'subtotal_beli' => 'decimal:2',
    ];

    public function barangKeluar()
    {
        return $this->belongsTo(BarangKeluar::class, 'barang_keluar_id');
    }

    public function accessory()
    {
        return $this->belongsTo(Accessory::class, 'accessory_id');
    }

    protected static function booted()
    {
        static::created(function ($item) {
            if ($item->accessory) {
                $item->accessory->decrement('stok', $item->qty);
            }
        });

        static::deleted(function ($item) {
            if ($item->accessory) {
                $item->accessory->increment('stok', $item->qty);
            }
        });
    }
}
