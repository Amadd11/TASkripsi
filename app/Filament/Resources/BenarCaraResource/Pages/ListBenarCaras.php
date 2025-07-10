<?php

namespace App\Filament\Resources\BenarCaraResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\BenarCaraResource;
use App\Filament\Widgets\PersentaseKepatuhan\BenarCaraPercentageStats;

class ListBenarCaras extends ListRecords
{
    protected static string $resource = BenarCaraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            BenarCaraPercentageStats::class,
        ];
    }
}
