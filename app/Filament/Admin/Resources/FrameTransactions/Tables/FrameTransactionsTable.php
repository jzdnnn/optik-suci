<?php

namespace App\Filament\Admin\Resources\FrameTransactions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class FrameTransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("patient.nama")
                    ->label("Nama Pasien")
                    ->searchable()
                    ->sortable(),
                TextColumn::make("patient.no_bon")
                    ->label("No. Bon")
                    ->searchable(),
                TextColumn::make("frame.name")
                    ->label("Frame")
                    ->searchable()
                    ->sortable(),
                TextColumn::make("harga")
                    ->label("Harga")
                    ->money("IDR", locale: "id")
                    ->sortable(),
                TextColumn::make("tanggal_keluar")
                    ->label("Tanggal Keluar")
                    ->date("d F Y")
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make("kategori")
                    ->label("Filter Kategori Pasien")
                    ->options([
                        "Umum" => "Umum",
                        "BPJS Kelas 1" => "BPJS Kelas 1",
                        "BPJS Kelas 2" => "BPJS Kelas 2",
                        "BPJS Kelas 3" => "BPJS Kelas 3",
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (! empty($data["value"])) {
                            $query->whereHas("patient", function ($q) use ($data) {
                                $q->where("kategori", $data["value"]);
                            });
                        }
                        return $query;
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
