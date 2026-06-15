<?php

namespace App\Filament\Admin\Resources\LensCategories\Pages;

use App\Filament\Admin\Resources\LensCategories\LensCategoryResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateLensCategory extends CreateRecord
{
    protected ?string $maxWidth = "full";

    protected static string $resource = LensCategoryResource::class;

    public function getMaxContentWidth(): Width | string | null
    {
        return Width::Full;
    }
}
