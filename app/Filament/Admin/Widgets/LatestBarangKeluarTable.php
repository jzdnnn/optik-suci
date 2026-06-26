<?php

namespace App\Filament\Admin\Widgets;

use App\Models\BarangKeluar;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestBarangKeluarTable extends TableWidget
{
    protected static ?string $heading = 'Barang Keluar (Transaksi) Terbaru';
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                BarangKeluar::query()
                    ->latest('tanggal_transaksi')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('patient.name')
                    ->label('Pasien/Pelanggan')
                    ->searchable(),
                TextColumn::make('tipe_transaksi')
                    ->label('Tipe Transaksi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'frame' => 'info',
                        'lensa' => 'warning',
                        'lengkap' => 'success',
                    }),
                TextColumn::make('total_transaksi')
                    ->label('Total Transaksi')
                    ->money('IDR', locale: 'id'),
                TextColumn::make('status_pembayaran')
                    ->label('Status Pembayaran')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'lunas' => 'success',
                        'dp' => 'warning',
                        'belum_bayar' => 'danger',
                    }),
                TextColumn::make('tanggal_transaksi')
                    ->label('Tanggal Transaksi')
                    ->date('d M Y'),
            ])
            ->paginated(false);
    }
}
