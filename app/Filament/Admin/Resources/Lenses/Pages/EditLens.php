<?php

namespace App\Filament\Admin\Resources\Lenses\Pages;

use App\Filament\Admin\Resources\Lenses\LensResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditLens extends EditRecord
{
    protected ?string $maxWidth = "full";

    protected static string $resource = LensResource::class;

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
