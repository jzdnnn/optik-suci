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
                TextColumn::make('name')
                    ->searchable()
                    ->label('Nama Lensa'),
                TextColumn::make('lensType.name')
                    ->label('Jenis Lensa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('bahan_lensa')
                    ->searchable(),
                TextColumn::make('lensOwnershipCategory.name')
                    ->label('Kategori Kepemilikan')
                    ->sortable(),
                TextColumn::make('harga_beli')
                    ->money('idr')
                    ->sortable(),
                TextColumn::make('harga_jual')
                    ->money('idr')
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
