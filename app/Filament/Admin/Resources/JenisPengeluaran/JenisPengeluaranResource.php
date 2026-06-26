<?php

namespace App\Filament\Admin\Resources\JenisPengeluaran;

use App\Filament\Admin\Resources\JenisPengeluaran\Pages\ManageJenisPengeluaran;
use App\Models\JenisPengeluaran;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class JenisPengeluaranResource extends Resource
{
    protected static ?string $model = JenisPengeluaran::class;

    protected static ?string $modelLabel = 'Kategori Pengeluaran';
    protected static ?string $pluralModelLabel = 'Kategori Pengeluaran';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-list-bullet';
    protected static string|\UnitEnum|null $navigationGroup = 'Kategori';

    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->label('Nama Pengeluaran')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Select::make('tipe')
                    ->label('Tipe / Group Pengeluaran')
                    ->options([
                        'operasional' => 'Operasional Harian',
                        'stok' => 'Stok & Persediaan',
                        'gaji' => 'Gaji & Lainnya',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama Pengeluaran')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tipe')
                    ->label('Tipe / Group')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'operasional' => 'success',
                        'stok' => 'warning',
                        'gaji' => 'primary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'operasional' => 'Operasional Harian',
                        'stok' => 'Stok & Persediaan',
                        'gaji' => 'Gaji & Lainnya',
                    })
                    ->sortable(),
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
            'index' => ManageJenisPengeluaran::route('/'),
        ];
    }
}
