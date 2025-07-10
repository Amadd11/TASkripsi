<?php

namespace App\Filament\Resources\MasterPetugasResource\Pages;

use App\Filament\Resources\MasterPetugasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMasterPetugas extends ListRecords
{
    protected static string $resource = MasterPetugasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
