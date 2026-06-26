<?php

namespace App\Filament\Admin\Resources\LaporanBulanan\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\LaporanBulanan;

class LaporanBulananTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('cabang')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nama_bulan')
                    ->label('Bulan')
                    ->sortable(query: function ($query, string $direction) {
                        return $query->orderBy('bulan', $direction);
                    }),
                TextColumn::make('tahun')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('total_pendapatan')
                    ->label('Total Pendapatan')
                    ->money('idr')
                    ->sortable(query: function ($query, string $direction) {
                        return $query->orderByRaw('(omzet + pendapatan_bpjs + pendapatan_harian) ' . $direction);
                    }),
                TextColumn::make('total_seluruh_pengeluaran')
                    ->label('Total Pengeluaran')
                    ->money('idr')
                    ->sortable(query: function ($query, string $direction) {
                        return $query->orderByRaw('(uang_makan + bonus_frame + bpjs_karyawan + speedy_internet + listrik + jne + faset_finish + faset_habis + keperluan_lain + total_frame + domba_mas + essilor + polycore + lensa_stok + lap_cepuk + gaji_pegawai + sewa_tempat + pajak + alat_alat) ' . $direction);
                    }),
                TextColumn::make('laba_bersih')
                    ->label('Laba Bersih')
                    ->money('idr')
                    ->color(fn ($state) => $state >= 0 ? 'success' : 'danger')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                Action::make('print')
                    ->label('Cetak')
                    ->icon('heroicon-o-printer')
                    ->color('warning')
                    ->url(fn (LaporanBulanan $record): string => route('laporan-bulanan.print', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
