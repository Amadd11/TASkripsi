<?php

namespace App\Filament\Resources\BenarReaksiMakananResource\Pages;

use App\Filament\Resources\BenarReaksiMakananResource;
use App\Filament\Widgets\PersentaseKepatuhan\BenarReaksiMakananPercentageStats;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBenarReaksiMakanans extends ListRecords
{
    protected static string $resource = BenarReaksiMakananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
     protected function getFooterWidgets(): array
    {
        return [
            BenarReaksiMakananPercentageStats::class
        ];
    }
}
