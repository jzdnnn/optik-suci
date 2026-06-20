<?php

namespace App\Filament\Admin\Resources\BarangMasuk;

use App\Filament\Admin\Resources\BarangMasuk\Pages\CreateBarangMasuk;
use App\Filament\Admin\Resources\BarangMasuk\Pages\EditBarangMasuk;
use App\Filament\Admin\Resources\BarangMasuk\Pages\ListBarangMasuk;
use App\Filament\Admin\Resources\BarangMasuk\Schemas\BarangMasukForm;
use App\Filament\Admin\Resources\BarangMasuk\Tables\BarangMasukTable;
use App\Models\BarangMasuk;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BarangMasukResource extends Resource
{
    protected static ?string $model = BarangMasuk::class;

    protected static ?string $modelLabel = 'Data Barang Masuk';
    protected static ?string $pluralModelLabel = 'Data Barang Masuk';

    protected static string|\UnitEnum|null $navigationGroup = 'Manajemen Stok';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return BarangMasukForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BarangMasukTable::configure($table);
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
            'index' => ListBarangMasuk::route('/'),
            'create' => CreateBarangMasuk::route('/create'),
            'edit' => EditBarangMasuk::route('/{record}/edit'),
        ];
    }
}
