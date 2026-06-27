<?php

namespace App\Filament\Admin\Resources\BarangMasuk\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BarangMasukTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('barang_masukable_type')
                    ->label('Jenis')
                    ->formatStateUsing(fn (string $state): string => class_basename($state)),
                TextColumn::make('barangMasukable.name')
                    ->label('Nama Barang')
                    ->searchable(),
                TextColumn::make('jenis_lensa')
                    ->label('Jenis Lensa')
                    ->getStateUsing(function ($record) {
                        if ($record->barang_masukable_type === \App\Models\Lens::class && $record->barangMasukable) {
                            return $record->barangMasukable->lensType?->name ?? '-';
                        }
                        return '-';
                    }),
                TextColumn::make('jenis_kepemilikan')
                    ->label('Jenis Kepemilikan')
                    ->getStateUsing(function ($record) {
                        if ($record->barang_masukable_type === \App\Models\Lens::class && $record->barangMasukable) {
                            return $record->barangMasukable->lensOwnershipCategory?->type ?? '-';
                        }
                        return '-';
                    }),
                TextColumn::make('stok')
                    ->label('Sisa Stok')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tanggal_masuk')
                    ->label('Tanggal Masuk Terakhir')
                    ->date()
                    ->sortable(),
            ]);
    }
}
