<?php

namespace App\Filament\Admin\Resources\LensOwnershipCategories;

use App\Filament\Admin\Resources\LensOwnershipCategories\Pages\ManageLensOwnershipCategories;
use App\Models\LensOwnershipCategory;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LensOwnershipCategoryResource extends Resource
{
    protected static ?string $model = LensOwnershipCategory::class;

    protected static ?string $modelLabel = 'Kategori Kepemilikan Lensa';
    protected static ?string $pluralModelLabel = 'Kategori Kepemilikan Lensa';

    protected static string|\UnitEnum|null $navigationGroup = 'Katalog';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bookmark';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Kategori')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Kategori')
                    ->searchable(),
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
            'index' => ManageLensOwnershipCategories::route('/'),
        ];
    }
}
