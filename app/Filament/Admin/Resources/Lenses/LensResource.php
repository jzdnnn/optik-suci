<?php

namespace App\Filament\Admin\Resources\Lenses;

use App\Filament\Admin\Resources\Lenses\Pages\CreateLens;
use App\Filament\Admin\Resources\Lenses\Pages\EditLens;
use App\Filament\Admin\Resources\Lenses\Pages\ListLenses;
use App\Filament\Admin\Resources\Lenses\Schemas\LensForm;
use App\Filament\Admin\Resources\Lenses\Tables\LensesTable;
use App\Models\Lens;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LensResource extends Resource
{
    protected static ?string $model = Lens::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEye;

    protected static string|\UnitEnum|null $navigationGroup = 'Katalog';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return LensForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LensesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLenses::route('/'),
            'create' => CreateLens::route('/create'),
            'edit' => EditLens::route('/{record}/edit'),
        ];
    }
}
