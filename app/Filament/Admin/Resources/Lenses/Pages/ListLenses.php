<?php

namespace App\Filament\Admin\Resources\Lenses\Pages;

use App\Filament\Admin\Resources\Lenses\LensResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLenses extends ListRecords
{
    protected static string $resource = LensResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
