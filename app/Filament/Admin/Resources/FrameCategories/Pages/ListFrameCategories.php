<?php

namespace App\Filament\Admin\Resources\FrameCategories\Pages;

use App\Filament\Admin\Resources\FrameCategories\FrameCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFrameCategories extends ListRecords
{
    protected static string $resource = FrameCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
