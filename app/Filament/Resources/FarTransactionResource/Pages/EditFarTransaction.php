<?php

namespace App\Filament\Resources\FarTransactionResource\Pages;

use App\Filament\Resources\FarTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFarTransaction extends EditRecord
{
    protected static string $resource = FarTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
