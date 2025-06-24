<?php

namespace App\Filament\Resources\FarTransactionResource\Pages;

use App\Filament\Resources\FarTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFarTransactions extends ListRecords
{
    protected static string $resource = FarTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
