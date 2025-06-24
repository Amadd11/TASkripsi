<?php

namespace App\Filament\Resources\BenarWaktuResource\Pages;

use App\Filament\Resources\BenarWaktuResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBenarWaktus extends ListRecords
{
    protected static string $resource = BenarWaktuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
