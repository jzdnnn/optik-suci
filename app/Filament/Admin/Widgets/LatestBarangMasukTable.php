<?php

namespace App\Filament\Admin\Widgets;

use App\Models\RiwayatBarangMasuk;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestBarangMasukTable extends TableWidget
{
    protected static ?string $heading = 'Barang Masuk Terbaru';
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                RiwayatBarangMasuk::query()
                    ->where('jenis_pergerakan', 'masuk')
                    ->latest('tanggal')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('barangMasuk.barang_masukable_type')
                    ->label('Jenis')
                    ->formatStateUsing(fn (string $state): string => class_basename($state)),
                TextColumn::make('barangMasuk.barangMasukable.name')
                    ->label('Nama Barang'),
                TextColumn::make('jumlah')
                    ->label('Jumlah (Pcs)')
                    ->numeric(),
                TextColumn::make('tanggal')
                    ->label('Tanggal Masuk')
                    ->dateTime('d M Y H:i'),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->placeholder('-'),
            ])
            ->paginated(false);
    }
}
