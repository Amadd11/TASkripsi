<?php

namespace App\Filament\Resources\BenarPengkajianResource\Pages;

use App\Filament\Resources\BenarPengkajianResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBenarPengkajian extends EditRecord
{
    protected static string $resource = BenarPengkajianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
