<?php

namespace App\Filament\Resources\BenarHakClientResource\Pages;

use App\Filament\Resources\BenarHakClientResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBenarHakClients extends ListRecords
{
    protected static string $resource = BenarHakClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
