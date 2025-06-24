<?php

namespace App\Filament\Resources\BenarObatResource\Pages;

use App\Filament\Resources\BenarObatResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBenarObats extends ListRecords
{
    protected static string $resource = BenarObatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
