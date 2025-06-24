<?php

namespace App\Filament\Resources\BenarDokumentasiResource\Pages;

use App\Filament\Resources\BenarDokumentasiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBenarDokumentasi extends EditRecord
{
    protected static string $resource = BenarDokumentasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
