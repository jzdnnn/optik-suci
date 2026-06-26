<?php

namespace App\Filament\Admin\Resources\Lenses\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;

class LensForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Lensa')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lensa')
                            ->required(),
                        Select::make('lens_type_id')
                            ->relationship('lensType', 'name')
                            ->label('Jenis Lensa')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Jenis Lensa Baru')
                                    ->required()
                                    ->unique('lens_types', 'name'),
                            ]),
                        Select::make('bahan_lensa')
                            ->label('Bahan Lensa')
                            ->options([
                                'Plastic' => 'Plastic',
                                'Glass' => 'Glass',
                            ]),
                        Select::make('jenis_kepemilikan')
                            ->label('Jenis Kepemilikan')
                            ->options([
                                'Stok Optik' => 'Stok Optik',
                                'Luar Optik' => 'Luar Optik',
                            ])
                            ->live()
                            ->afterStateHydrated(function ($state, $set, $record) {
                                if ($record && $record->lensOwnershipCategory) {
                                    $set('jenis_kepemilikan', $record->lensOwnershipCategory->type);
                                }
                            })
                            ->dehydrated(false),

                        Select::make('lens_ownership_category_id')
                            ->label('Kategori Kepemilikan')
                            ->options(fn ($get) => 
                                \App\Models\LensOwnershipCategory::query()
                                    ->when($get('jenis_kepemilikan'), fn ($query, $type) => $query->where('type', $type))
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm(fn ($get) => [
                                TextInput::make('name')
                                    ->label('Nama Kategori')
                                    ->required(),
                                Select::make('type')
                                    ->label('Jenis Kepemilikan')
                                    ->options([
                                        'Stok Optik' => 'Stok Optik',
                                        'Luar Optik' => 'Luar Optik',
                                    ])
                                    ->default($get('jenis_kepemilikan'))
                                    ->required()
                                    ->native(false),
                            ]),

                        TextInput::make('harga_beli')
                            ->label('Harga Beli')
                            ->numeric()
                            ->default(0),
                        TextInput::make('harga_jual')
                            ->label('Harga Jual')
                            ->numeric()
                            ->default(0),
                    ])->columns(['sm' => 1, 'md' => 2, 'lg' => 3])
                    ->columnSpanFull(),
            ]);
    }
}
