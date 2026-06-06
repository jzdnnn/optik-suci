<?php

namespace App\Filament\Admin\Resources\LensCategories;

use App\Filament\Admin\Resources\LensCategories\Pages\CreateLensCategory;
use App\Filament\Admin\Resources\LensCategories\Pages\EditLensCategory;
use App\Filament\Admin\Resources\LensCategories\Pages\ListLensCategories;
use App\Filament\Admin\Resources\LensCategories\Schemas\LensCategoryForm;
use App\Filament\Admin\Resources\LensCategories\Tables\LensCategoriesTable;
use App\Models\LensCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LensCategoryResource extends Resource
{
    protected static ?string $model = LensCategory::class;
    
    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return LensCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LensCategoriesTable::configure($table);
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
            'index' => ListLensCategories::route('/'),
            'create' => CreateLensCategory::route('/create'),
            'edit' => EditLensCategory::route('/{record}/edit'),
        ];
    }
}
