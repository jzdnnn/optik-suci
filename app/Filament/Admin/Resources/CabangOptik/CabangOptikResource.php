<?php

namespace App\Filament\Admin\Resources\CabangOptik;

use App\Models\CabangOptik;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CabangOptikResource extends Resource
{
    protected static ?string $model = CabangOptik::class;

    protected static ?string $modelLabel = 'Cabang Optik';
    protected static ?string $pluralModelLabel = 'Cabang Optik';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';
    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Cabang')
                ->description('Data identitas dan alamat cabang optik.')
                ->schema([
                    TextInput::make('nama')
                        ->label('Nama Cabang')
                        ->placeholder('Contoh: OPTIK SUCI NO 1 CIMAHI')
                        ->required()
                        ->maxLength(255)
                        ->afterStateUpdated(fn ($state, $set) => $set('nama', strtoupper($state)))
                        ->live(onBlur: true)
                        ->columnSpan(2),
                    TextInput::make('alamat')
                        ->label('Alamat (Opsional)')
                        ->maxLength(500)
                        ->columnSpan(2),
                    Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true)
                        ->columnSpan(2),
                ])
                ->columns(2)
                ->columnSpanFull(),

            Section::make('Saldo Awal')
                ->description('Saldo kas awal sebelum periode pencatatan dimulai untuk cabang ini. Digunakan sebagai dasar perhitungan Laporan Keuangan Harian.')
                ->icon('heroicon-o-banknotes')
                ->schema([
                    TextInput::make('saldo_awal')
                        ->label('Saldo Awal (Rp)')
                        ->helperText('Masukkan jumlah kas yang sudah tersedia sebelum transaksi pertama dicatat. Isi 0 jika dimulai dari nol.')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0)
                        ->minValue(0)
                        ->required()
                        ->columnSpanFull(),
                ])
                ->columns(1)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama Cabang')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('alamat')
                    ->label('Alamat')
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('saldo_awal')
                    ->label('Saldo Awal')
                    ->money('IDR', 0)
                    ->sortable()
                    ->alignRight(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label('Terakhir Update')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
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
            'index' => Pages\ManageCabangOptik::route('/'),
        ];
    }
}
