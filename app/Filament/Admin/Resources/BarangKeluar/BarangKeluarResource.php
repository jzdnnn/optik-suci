<?php

namespace App\Filament\Admin\Resources\BarangKeluar;

use App\Filament\Admin\Resources\BarangKeluar\Pages\CreateBarangKeluar;
use App\Filament\Admin\Resources\BarangKeluar\Pages\EditBarangKeluar;
use App\Filament\Admin\Resources\BarangKeluar\Pages\ListBarangKeluar;
use App\Filament\Admin\Resources\BarangKeluar\Schemas\BarangKeluarForm;
use App\Filament\Admin\Resources\BarangKeluar\Tables\BarangKeluarTable;
use App\Models\BarangKeluar;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BarangKeluarResource extends Resource
{
    protected static ?string $model = BarangKeluar::class;

    protected static ?string $modelLabel = 'Data Barang Keluar';
    protected static ?string $pluralModelLabel = 'Data Barang Keluar';

    protected static string|\UnitEnum|null $navigationGroup = 'Manajemen Stok';

    protected static ?string $recordTitleAttribute = 'no_bon';

    public static function getGloballySearchableAttributes(): array
    {
        return ['no_bon', 'patient.nama'];
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;

    public static function form(Schema $schema): Schema
    {
        return \App\Filament\Admin\Resources\BarangKeluar\Schemas\BarangKeluarForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return \App\Filament\Admin\Resources\BarangKeluar\Tables\BarangKeluarTable::configure($table);
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
            'index' => ListBarangKeluar::route('/'),
            'create' => CreateBarangKeluar::route('/create'),
            'edit' => EditBarangKeluar::route('/{record}/edit'),
        ];
    }
}
