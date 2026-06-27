<?php

namespace App\Filament\Admin\Resources\BarangMasuk\Pages;

use App\Filament\Admin\Resources\BarangMasuk\BarangMasukResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBarangMasuk extends EditRecord
{
    protected static string $resource = BarangMasukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $record = $this->record;
        
        if ($record->wasChanged('stok')) {
            $old = (int) $record->getOriginal('stok');
            $new = (int) $record->stok;
            $diff = $new - $old;
            
            if ($diff !== 0) {
                \App\Models\RiwayatBarangMasuk::create([
                    'barang_masuk_id' => $record->id,
                    'jenis_pergerakan' => $diff > 0 ? 'masuk' : 'keluar',
                    'jumlah' => abs($diff),
                    'keterangan' => 'Koreksi/Update Stok via Panel (dari ' . $old . ' ke ' . $new . ')',
                    'tanggal' => now(),
                ]);
            }
        }
    }
}
