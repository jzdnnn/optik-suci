<?php

namespace App\Filament\Admin\Resources\Accessories;

use App\Filament\Admin\Resources\Accessories\Pages\ManageAccessories;
use App\Models\Accessory;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AccessoryResource extends Resource
{
    protected static ?string $model = Accessory::class;

    protected static ?string $modelLabel = 'Aksesoris';
    protected static ?string $pluralModelLabel = 'Data Aksesoris';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';
    protected static string|\UnitEnum|null $navigationGroup = 'Katalog';

    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->label('Nama Aksesoris')
                    ->required()
                    ->maxLength(255),
                TextInput::make('stok')
                    ->label('Stok')
                    ->numeric()
                    ->default(0)
                    ->required(),
                TextInput::make('harga_beli')
                    ->label('Harga Beli')
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0)
                    ->required(),
                TextInput::make('harga_jual')
                    ->label('Harga Jual')
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama Aksesoris')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('stok')
                    ->label('Stok Available')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('harga_beli')
                    ->label('Harga Beli')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('harga_jual')
                    ->label('Harga Jual')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAccessories::route('/'),
        ];
    }
}
