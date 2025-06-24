<?php

namespace App\Filament\Resources\MasterPasienResource\Pages;

use App\Filament\Resources\MasterPasienResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMasterPasien extends EditRecord
{
    protected static string $resource = MasterPasienResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
