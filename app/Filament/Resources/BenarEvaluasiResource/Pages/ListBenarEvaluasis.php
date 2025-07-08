<?php

namespace App\Filament\Resources\BenarEvaluasiResource\Pages;

use App\Filament\Resources\BenarEvaluasiResource;
use App\Filament\Widgets\PersentaseKepatuhan\BenarEvaluasiPercentageStats;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBenarEvaluasis extends ListRecords
{
    protected static string $resource = BenarEvaluasiResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getFooterWidgets(): array
    {
        return [
            BenarEvaluasiPercentageStats::class,
        ];
    }
}
