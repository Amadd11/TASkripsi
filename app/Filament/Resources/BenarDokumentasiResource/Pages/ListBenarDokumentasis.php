<?php

namespace App\Filament\Resources\BenarDokumentasiResource\Pages;

use App\Filament\Resources\BenarDokumentasiResource;
use App\Filament\Widgets\PersentaseKepatuhan\BenarDokumentasiPercentageStats;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBenarDokumentasis extends ListRecords
{
    protected static string $resource = BenarDokumentasiResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getFooterWidgets(): array
    {
        return [
            BenarDokumentasiPercentageStats::class,
        ];
    }
}
