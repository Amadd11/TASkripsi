<?php

namespace App\Filament\Resources\BenarPasienResource\Pages;

use App\Filament\Resources\BenarPasienResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBenarPasiens extends ListRecords
{
    protected static string $resource = BenarPasienResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
