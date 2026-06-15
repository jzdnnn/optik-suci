<?php

namespace App\Filament\Admin\Resources\FrameTransactions\Pages;

use App\Filament\Admin\Resources\FrameTransactions\FrameTransactionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFrameTransaction extends EditRecord
{
    protected static string $resource = FrameTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
