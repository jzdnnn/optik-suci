<?php

namespace App\Filament\Admin\Resources\LaporanBulanan;

use App\Filament\Admin\Resources\LaporanBulanan\Pages\CreateLaporanBulanan;
use App\Filament\Admin\Resources\LaporanBulanan\Pages\EditLaporanBulanan;
use App\Filament\Admin\Resources\LaporanBulanan\Pages\ListLaporanBulanan;
use App\Filament\Admin\Resources\LaporanBulanan\Schemas\LaporanBulananForm;
use App\Filament\Admin\Resources\LaporanBulanan\Tables\LaporanBulananTable;
use App\Models\LaporanBulanan;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class LaporanBulananResource extends Resource
{
    protected static ?string $model = LaporanBulanan::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Laporan Bulanan';

    protected static ?string $modelLabel = 'Laporan Bulanan';

    protected static ?string $pluralModelLabel = 'Laporan Bulanan';

    protected static string|\UnitEnum|null $navigationGroup = 'Laporan';

    public static function form(Schema $schema): Schema
    {
        return LaporanBulananForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LaporanBulananTable::configure($table);
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
            'index' => ListLaporanBulanan::route('/'),
            'create' => CreateLaporanBulanan::route('/create'),
            'edit' => EditLaporanBulanan::route('/{record}/edit'),
        ];
    }
}
