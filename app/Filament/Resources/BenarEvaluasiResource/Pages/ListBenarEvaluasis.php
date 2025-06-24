<?php

namespace App\Filament\Resources\BenarEvaluasiResource\Pages;

use App\Filament\Resources\BenarEvaluasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBenarEvaluasis extends ListRecords
{
    protected static string $resource = BenarEvaluasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
