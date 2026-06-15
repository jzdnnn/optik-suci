<?php

namespace App\Filament\Admin\Resources\FrameTransactions;

use App\Filament\Admin\Resources\FrameTransactions\Pages\CreateFrameTransaction;
use App\Filament\Admin\Resources\FrameTransactions\Pages\EditFrameTransaction;
use App\Filament\Admin\Resources\FrameTransactions\Pages\ListFrameTransactions;
use App\Filament\Admin\Resources\FrameTransactions\Schemas\FrameTransactionForm;
use App\Filament\Admin\Resources\FrameTransactions\Tables\FrameTransactionsTable;
use App\Models\FrameTransaction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FrameTransactionResource extends Resource
{
    protected static ?string $model = FrameTransaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $modelLabel = 'Frame Keluar';
    protected static ?string $pluralModelLabel = 'Data Frame Keluar';
    protected static string|\UnitEnum|null $navigationGroup = 'Katalog';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return FrameTransactionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FrameTransactionsTable::configure($table);
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
            'index' => ListFrameTransactions::route('/'),
            'create' => CreateFrameTransaction::route('/create'),
            'edit' => EditFrameTransaction::route('/{record}/edit'),
        ];
    }
}
