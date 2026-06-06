<?php

namespace App\Filament\Admin\Resources\FrameCategories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FrameCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
            ]);
    }
}
