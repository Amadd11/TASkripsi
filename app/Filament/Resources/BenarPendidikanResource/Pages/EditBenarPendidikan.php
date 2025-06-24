<?php

namespace App\Filament\Resources\BenarPendidikanResource\Pages;

use App\Filament\Resources\BenarPendidikanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBenarPendidikan extends EditRecord
{
    protected static string $resource = BenarPendidikanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
