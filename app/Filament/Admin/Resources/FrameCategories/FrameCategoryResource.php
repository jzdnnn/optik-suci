<?php

namespace App\Filament\Admin\Resources\FrameCategories;

use App\Filament\Admin\Resources\FrameCategories\Pages\CreateFrameCategory;
use App\Filament\Admin\Resources\FrameCategories\Pages\EditFrameCategory;
use App\Filament\Admin\Resources\FrameCategories\Pages\ListFrameCategories;
use App\Filament\Admin\Resources\FrameCategories\Schemas\FrameCategoryForm;
use App\Filament\Admin\Resources\FrameCategories\Tables\FrameCategoriesTable;
use App\Models\FrameCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FrameCategoryResource extends Resource
{
    protected static ?string $model = FrameCategory::class;
    
    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return FrameCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FrameCategoriesTable::configure($table);
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
            'index' => ListFrameCategories::route('/'),
            'create' => CreateFrameCategory::route('/create'),
            'edit' => EditFrameCategory::route('/{record}/edit'),
        ];
    }
}
