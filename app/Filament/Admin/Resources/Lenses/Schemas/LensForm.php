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
                        Select::make('jenis_lensa')
                            ->label('Jenis Lensa')
                            ->options([
                                'Single Vision' => 'Single Vision',
                                'Kryptok' => 'Kryptok',
                                'Flat top' => 'Flat top',
                                'Progressive' => 'Progressive',
                                'Poly Carbonate' => 'Poly Carbonate',
                            ]),
                        Select::make('bahan_lensa')
                            ->label('Bahan Lensa')
                            ->options([
                                'Plastic' => 'Plastic',
                                'Glass' => 'Glass',
                            ]),
                        Select::make('lens_ownership_category_id')
                            ->relationship('lensOwnershipCategory', 'name')
                            ->label('Kategori Kepemilikan')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Nama Kategori')
                                    ->required(),
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
