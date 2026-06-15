<?php

namespace App\Filament\Admin\Resources\FrameTransactions\Pages;

use App\Filament\Admin\Resources\FrameTransactions\FrameTransactionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFrameTransactions extends ListRecords
{
    protected static string $resource = FrameTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
