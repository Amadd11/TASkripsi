<?php

namespace App\Filament\Resources\BenarDosisResource\Pages;

use App\Filament\Resources\BenarDosisResource;
use App\Filament\Widgets\PersentaseKepatuhan\BenarDosisPercentageStats;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBenarDoses extends ListRecords
{
    protected static string $resource = BenarDosisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    protected function getFooterWidgets(): array
    {
        return [
            BenarDosisPercentageStats::class
        ];
    }
}
