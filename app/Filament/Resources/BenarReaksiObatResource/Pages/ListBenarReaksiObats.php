<?php

namespace App\Filament\Resources\BenarReaksiObatResource\Pages;

use App\Filament\Resources\BenarReaksiObatResource;
use App\Filament\Widgets\PersentaseKepatuhan\BenarReaksiObatPercentageStats;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBenarReaksiObats extends ListRecords
{
    protected static string $resource = BenarReaksiObatResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
    protected function getFooterWidgets(): array
    {
        return [
            BenarReaksiObatPercentageStats::class
        ];
    }
}
