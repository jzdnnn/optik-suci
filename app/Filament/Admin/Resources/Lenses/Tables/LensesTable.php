<?php

namespace App\Filament\Admin\Resources\Lenses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ukuran')
                    ->searchable(),
                TextColumn::make('jenis_lensa')
                    ->searchable(),
                TextColumn::make('index_bias')
                    ->searchable(),
                TextColumn::make('bahan_lensa')
                    ->searchable(),
                TextColumn::make('lens_category_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('jenis_tipe')
                    ->searchable(),
                TextColumn::make('total_pasang')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
