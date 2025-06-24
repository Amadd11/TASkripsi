<?php

namespace App\Filament\Resources\BenarDosisResource\Pages;

use App\Filament\Resources\BenarDosisResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBenarDoses extends ListRecords
{
    protected static string $resource = BenarDosisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
