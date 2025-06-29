<?php

namespace App\Filament\Resources\BenarPendidikanResource\Pages;

use App\Filament\Resources\BenarPendidikanResource;
use App\Filament\Widgets\PersentaseKepatuhan\BenarPendidikanPercentageStats;
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

    protected function getFooterWidgets(): array
    {
        return [
            BenarPendidikanPercentageStats::class
        ];
    }
}
