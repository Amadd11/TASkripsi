<?php

namespace App\Filament\Resources\BenarReaksiObatResource\Pages;

use App\Filament\Resources\BenarReaksiObatResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBenarReaksiObat extends EditRecord
{
    protected static string $resource = BenarReaksiObatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
