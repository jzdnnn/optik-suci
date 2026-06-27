<?php

namespace App\Filament\Admin\Resources\Accessories\Pages;

use App\Filament\Admin\Resources\Accessories\AccessoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageAccessories extends ManageRecords
{
    protected static string $resource = AccessoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
