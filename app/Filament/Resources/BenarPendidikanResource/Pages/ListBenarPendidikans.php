<?php

namespace App\Filament\Resources\BenarPendidikanResource\Pages;

use App\Filament\Resources\BenarPendidikanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBenarPendidikans extends ListRecords
{
    protected static string $resource = BenarPendidikanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
