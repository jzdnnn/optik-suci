<?php

namespace App\Filament\Admin\Resources\Frames;

use App\Filament\Admin\Resources\Frames\Pages\CreateFrame;
use App\Filament\Admin\Resources\Frames\Pages\EditFrame;
use App\Filament\Admin\Resources\Frames\Pages\ListFrames;
use App\Filament\Admin\Resources\Frames\Schemas\FrameForm;
use App\Filament\Admin\Resources\Frames\Tables\FramesTable;
use App\Models\Frame;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FrameResource extends Resource
{
    protected static ?string $model = Frame::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquare3Stack3d;

    protected static string|\UnitEnum|null $navigationGroup = 'Katalog';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return FrameForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FramesTable::configure($table);
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
            'index' => ListFrames::route('/'),
            'create' => CreateFrame::route('/create'),
            'edit' => EditFrame::route('/{record}/edit'),
        ];
    }
}
