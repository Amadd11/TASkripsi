<?php

namespace App\Filament\Resources\BenarHakClientResource\Pages;

use App\Filament\Resources\BenarHakClientResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBenarHakClient extends EditRecord
{
    protected static string $resource = BenarHakClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
