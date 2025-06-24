<?php

namespace App\Filament\Resources\BenarReaksiMakananResource\Pages;

use App\Filament\Resources\BenarReaksiMakananResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBenarReaksiMakanan extends EditRecord
{
    protected static string $resource = BenarReaksiMakananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
