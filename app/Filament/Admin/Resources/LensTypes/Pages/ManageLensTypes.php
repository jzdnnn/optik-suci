<?php

namespace App\Filament\Admin\Resources\LensTypes\Pages;

use App\Filament\Admin\Resources\LensTypes\LensTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageLensTypes extends ManageRecords
{
    protected static string $resource = LensTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
