<?php

namespace App\Filament\Admin\Resources\Lenses\Pages;

use App\Filament\Admin\Resources\Lenses\LensResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLens extends EditRecord
{
    protected static string $resource = LensResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
