<?php

namespace App\Filament\Admin\Resources\CabangOptik\Pages;

use App\Filament\Admin\Resources\CabangOptik\CabangOptikResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageCabangOptik extends ManageRecords
{
    protected static string $resource = CabangOptikResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
