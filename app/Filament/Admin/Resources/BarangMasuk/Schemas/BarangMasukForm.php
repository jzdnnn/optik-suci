<?php

namespace App\Filament\Admin\Resources\BarangMasuk\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;

class BarangMasukForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('barang_masukable_type')
                    ->label('Jenis Barang')
                    ->options([
                        \App\Models\Frame::class => 'Frame',
                        \App\Models\Lens::class => 'Lensa',
                    ])
                    ->live()
                    ->required(),
                Select::make('barang_masukable_id')
                    ->label('Pilih Barang')
                    ->options(function (Get $get) {
                        $type = $get('barang_masukable_type');
                        if ($type === \App\Models\Frame::class) {
                            return \App\Models\Frame::pluck('name', 'id');
                        } elseif ($type === \App\Models\Lens::class) {
                            return \App\Models\Lens::with(['lensType', 'lensOwnershipCategory'])
                                ->get()
                                ->mapWithKeys(function ($lens) {
                                    $typeName = $lens->lensType?->name ?? 'Tanpa Jenis';
                                    $catName = $lens->lensOwnershipCategory?->name ?? 'Tanpa Kategori';
                                    return [$lens->id => "{$lens->name} - {$typeName} - {$catName}"];
                                });
                        }
                        return [];
                    })
                    ->searchable()
                    ->required(),
                TextInput::make('stok')
                    ->label('Jumlah Stok')
                    ->numeric()
                    ->required(),
            ]);
    }
}
