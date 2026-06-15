<?php

namespace App\Filament\Admin\Resources\LensCategories\Pages;

use App\Filament\Admin\Resources\LensCategories\LensCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditLensCategory extends EditRecord
{
    protected ?string $maxWidth = "full";

    protected static string $resource = LensCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    public function getMaxContentWidth(): Width | string | null
    {
        return Width::Full;
    }
}
