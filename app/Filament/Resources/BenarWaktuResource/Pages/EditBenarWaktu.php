<?php

namespace App\Filament\Resources\BenarWaktuResource\Pages;

use App\Filament\Resources\BenarWaktuResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBenarWaktu extends EditRecord
{
    protected static string $resource = BenarWaktuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
