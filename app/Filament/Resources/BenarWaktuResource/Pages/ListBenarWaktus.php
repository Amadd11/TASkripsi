<?php

namespace App\Filament\Resources\BenarWaktuResource\Pages;

use App\Filament\Resources\BenarWaktuResource;
use App\Filament\Widgets\PersentaseKepatuhan\BenarWaktuPercentageStats;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBenarWaktus extends ListRecords
{
    protected static string $resource = BenarWaktuResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
    protected function getFooterWidgets(): array
    {
        return [
            BenarWaktuPercentageStats::class
        ];
    }
}
