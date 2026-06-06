<?php

namespace App\Filament\Admin\Resources\FrameCategories\Pages;

use App\Filament\Admin\Resources\FrameCategories\FrameCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFrameCategory extends EditRecord
{
    protected static string $resource = FrameCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
