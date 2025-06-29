<?php

namespace App\Filament\Resources\BenarPengkajianResource\Pages;

use App\Filament\Resources\BenarPengkajianResource;
use App\Filament\Widgets\PersentaseKepatuhan\BenarPengkajianPercentageStats;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBenarPengkajians extends ListRecords
{
    protected static string $resource = BenarPengkajianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    protected function getFooterWidgets(): array
    {
        return [
            BenarPengkajianPercentageStats::class
        ];
    }
}
