<?php

namespace App\Filament\Resources\BenarReaksiObatResource\Pages;

use App\Filament\Resources\BenarReaksiObatResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBenarReaksiObats extends ListRecords
{
    protected static string $resource = BenarReaksiObatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
