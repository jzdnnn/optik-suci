<?php

namespace App\Filament\Admin\Resources\Pengeluaran;

use App\Filament\Admin\Resources\Pengeluaran\Pages\ManagePengeluaran;
use App\Models\Pengeluaran;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PengeluaranResource extends Resource
{
    protected static ?string $model = Pengeluaran::class;

    protected static ?string $modelLabel = 'Catat Pengeluaran';
    protected static ?string $pluralModelLabel = 'Catat Pengeluaran';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';
    protected static string|\UnitEnum|null $navigationGroup = 'Laporan';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('tanggal')
                    ->label('Tanggal Pengeluaran')
                    ->required()
                    ->default(now()),
                Select::make('jenis_pengeluaran_id')
                    ->label('Kategori Pengeluaran')
                    ->relationship('jenisPengeluaran', 'nama')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('cabang')
                    ->label('Cabang Optik')
                    ->default(fn () => session('cabang_nama', ''))
                    ->readOnly()
                    ->dehydrated()
                    ->required(),
                TextInput::make('nominal')
                    ->label('Nominal Pengeluaran')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
                TextInput::make('keterangan')
                    ->label('Keterangan')
                    ->placeholder('Contoh: Pembayaran JNE 12 Pcs')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('jenisPengeluaran.nama')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('cabang')
                    ->label('Cabang')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nominal')
                    ->label('Nominal')
                    ->money('idr')
                    ->sortable(),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePengeluaran::route('/'),
        ];
    }
}
