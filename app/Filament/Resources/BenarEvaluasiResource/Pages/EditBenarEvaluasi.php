<?php

namespace App\Filament\Resources\BenarEvaluasiResource\Pages;

use App\Filament\Resources\BenarEvaluasiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBenarEvaluasi extends EditRecord
{
    protected static string $resource = BenarEvaluasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
