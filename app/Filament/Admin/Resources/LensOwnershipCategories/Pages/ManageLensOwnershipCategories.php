<?php

namespace App\Filament\Admin\Resources\LensOwnershipCategories\Pages;

use App\Filament\Admin\Resources\LensOwnershipCategories\LensOwnershipCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageLensOwnershipCategories extends ManageRecords
{
    protected static string $resource = LensOwnershipCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
