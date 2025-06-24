<?php

namespace App\Filament\Resources\BenarCaraResource\Pages;

use App\Filament\Resources\BenarCaraResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBenarCara extends EditRecord
{
    protected static string $resource = BenarCaraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
