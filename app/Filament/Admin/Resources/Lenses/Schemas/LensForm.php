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
                        TextInput::make('index_bias')
                            ->label('Index Bias'),
                        TextInput::make('ukuran')
                            ->label('Ukuran'),
                        Select::make('lens_category_id')
                            ->relationship('lensCategory', 'name')
                            ->label('Kategori Kepemilikan'),
                        Select::make('jenis_tipe')
                            ->label('Jenis Tipe')
                            ->options([
                                'Finish' => 'Finish',
                                'RX (Dibuat)' => 'RX (Dibuat)',
                            ]),
                        TagsInput::make('accessories')
                            ->label('Aksesoris')
                            ->suggestions([
                                'Blu Ray',
                                'Photochromic',
                                'Blue Cromic',
                                'Night Vision',
                            ]),
                        TextInput::make('total_pasang')
                            ->label('Total Pasang')
                            ->numeric()
                            ->default(0),
                    ])->columns(['sm' => 1, 'md' => 2, 'lg' => 3, 'xl' => 4])
                    ->columnSpanFull(),
            ]);
    }
}
