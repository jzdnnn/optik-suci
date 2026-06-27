<?php

namespace App\Filament\Admin\Resources\BarangMasuk\Pages;

use App\Filament\Admin\Resources\BarangMasuk\BarangMasukResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBarangMasuk extends CreateRecord
{
    protected static string $resource = BarangMasukResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $barangMasuk = static::getModel()::firstOrCreate(
            [
                'barang_masukable_type' => $data['barang_masukable_type'],
                'barang_masukable_id' => $data['barang_masukable_id'],
            ],
            ['stok' => 0]
        );

        $barangMasuk->stok += $data['stok'];
        $barangMasuk->tanggal_masuk = now();
        $barangMasuk->save();

        \App\Models\RiwayatBarangMasuk::create([
            'barang_masuk_id' => $barangMasuk->id,
            'jenis_pergerakan' => 'masuk',
            'jumlah' => $data['stok'],
            'keterangan' => 'Penambahan Stok Manual via Panel',
            'tanggal' => now(),
        ]);

        return $barangMasuk;
    }
}
