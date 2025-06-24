<?php

namespace App\Filament\Resources\BenarDosisResource\Pages;

use App\Filament\Resources\BenarDosisResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBenarDosis extends EditRecord
{
    protected static string $resource = BenarDosisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
