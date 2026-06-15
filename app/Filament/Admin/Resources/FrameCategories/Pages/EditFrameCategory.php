<?php

namespace App\Filament\Admin\Resources\FrameCategories\Pages;

use App\Filament\Admin\Resources\FrameCategories\FrameCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditFrameCategory extends EditRecord
{
    protected ?string $maxWidth = "full";

    protected static string $resource = FrameCategoryResource::class;

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
