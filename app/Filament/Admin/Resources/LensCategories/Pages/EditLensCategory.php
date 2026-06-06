<?php

namespace App\Filament\Admin\Resources\LensCategories\Pages;

use App\Filament\Admin\Resources\LensCategories\LensCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLensCategory extends EditRecord
{
    protected static string $resource = LensCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
