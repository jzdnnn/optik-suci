<?php

namespace App\Filament\Admin\Resources\BarangKeluar\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;

class BarangKeluarTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_bon')
                    ->label('No BON')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('patient.nama')
                    ->label('Pasien')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tanggal_transaksi')
                    ->label('Tanggal Transaksi')
                    ->date()
                    ->sortable(),
                TextColumn::make('tanggal_pengambilan')
                    ->label('Tanggal Pengambilan')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('tipe_transaksi')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'frame' => 'warning',
                        'lensa' => 'info',
                        'lengkap' => 'success',
                    }),
                TextColumn::make('total_transaksi')
                    ->label('Total Harga')
                    ->money('idr')
                    ->sortable(),
                TextColumn::make('status_pembayaran')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'lunas' => 'success',
                        'dp' => 'warning',
                        'belum_bayar' => 'danger',
                    }),
            ])
            ->defaultSort('tanggal_transaksi', 'desc');
    }
}
