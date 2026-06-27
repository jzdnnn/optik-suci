<?php

namespace App\Filament\Admin\Resources\Roles;

use App\Filament\Admin\Resources\Roles\Pages\ManageRoles;
use Spatie\Permission\Models\Role;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $modelLabel = 'Role & Permission';
    protected static ?string $pluralModelLabel = 'Role & Permission';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';
    protected static string|\UnitEnum|null $navigationGroup = 'Sistem';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Role')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                
                Section::make('Hak Akses (Permissions)')
                    ->description('Pilih hak akses untuk role ini')
                    ->schema([
                        Grid::make(3)
                            ->schema(
                                collect([
                                    'user' => 'Pengguna (User)',
                                    'role' => 'Role & Permission',
                                    'frame_category' => 'Kategori Frame',
                                    'frame' => 'Data Frame',
                                    'lens_ownership_category' => 'Kategori Kepemilikan Lensa',
                                    'lens_type' => 'Jenis Lensa',
                                    'lens' => 'Data Lensa',
                                    'patient' => 'Data Pasien/Customer',
                                    'barang_masuk' => 'Barang Masuk',
                                    'barang_keluar' => 'Barang Keluar',
                                    'laporan_keuangan' => 'Laporan Keuangan',
                                    'laporan_bulanan' => 'Laporan Bulanan',
                                    'jenis_pengeluaran' => 'Kategori Pengeluaran',
                                    'pengeluaran' => 'Catat Pengeluaran',
                                    'setoran_mingguan' => 'Setoran Mingguan',
                                ])->map(function ($label, $resource) {
                                    return CheckboxList::make("permissions_{$resource}")
                                        ->label($label)
                                        ->options(function () use ($resource) {
                                            return \Spatie\Permission\Models\Permission::where('name', 'like', "%_{$resource}")
                                                ->get()
                                                ->pluck('name', 'id')
                                                ->map(fn ($name) => match(explode('_', $name)[0]) {
                                                    'viewAny' => 'Lihat Daftar (View Any)',
                                                    'view' => 'Lihat Detail (View)',
                                                    'create' => 'Tambah (Create)',
                                                    'update' => 'Ubah (Update)',
                                                    'delete' => 'Hapus (Delete)',
                                                    default => $name,
                                                })
                                                ->toArray();
                                        })
                                        ->afterStateHydrated(function ($component, $record) use ($resource) {
                                            if ($record) {
                                                $component->state(
                                                    $record->permissions()
                                                        ->where('name', 'like', "%_{$resource}")
                                                        ->pluck('id')
                                                        ->toArray()
                                                );
                                            }
                                        })
                                        ->dehydrated(false)
                                        ->saveRelationshipsUsing(function ($record, $state) use ($resource) {
                                            // Ambil ID permission yang bukan untuk resource ini
                                            $otherPermissions = $record->permissions()
                                                ->where('name', 'not like', "%_{$resource}")
                                                ->pluck('id')
                                                ->toArray();
                                            
                                            // Gabungkan dan sync
                                            $newPermissions = array_merge($otherPermissions, $state);
                                            $record->permissions()->sync($newPermissions);
                                        })
                                        ->bulkToggleable();
                                    })->values()->toArray()
                            )
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Role')
                    ->searchable(),
                TextColumn::make('permissions_count')
                    ->label('Jumlah Permission')
                    ->counts('permissions'),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y H:i'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageRoles::route('/'),
        ];
    }
}
