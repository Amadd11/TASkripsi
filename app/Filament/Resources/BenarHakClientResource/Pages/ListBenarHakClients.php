<?php

namespace App\Filament\Resources\BenarHakClientResource\Pages;

use App\Filament\Resources\BenarHakClientResource;
use App\Filament\Widgets\PersentaseKepatuhan\BenarHakClientPercentageStats;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBenarHakClients extends ListRecords
{
    protected static string $resource = BenarHakClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            BenarHakClientPercentageStats::class,
        ];
    }
}
