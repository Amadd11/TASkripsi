<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\BenarCaraStats;
use App\Filament\Widgets\BenarDokumentasiStats;
use App\Filament\Widgets\BenarDosisStats;
use App\Filament\Widgets\BenarEvaluasiStats;
use App\Filament\Widgets\BenarHakClientStats;
use App\Filament\Widgets\BenarObatStats;
use App\Filament\Widgets\BenarPasienStats;
use App\Filament\Widgets\BenarPendidikanStats;
use App\Filament\Widgets\BenarPengkajianStats;
use App\Filament\Widgets\BenarReaksiMakananStats;
use App\Filament\Widgets\BenarReaksiObatStats;
use App\Filament\Widgets\BenarWaktuStats;
use App\Filament\Widgets\RekapitulasiChart;
use Filament\Pages\Page;

class RekapitulasiChartPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.rekapitulasi-chart-page';

    protected static ?string $title = 'Rekapitulasi Chart';

    protected function getHeaderWidgets(): array
    {
        return [
            RekapitulasiChart::class,
            BenarCaraStats::class,
            BenarEvaluasiStats::class,
            BenarDosisStats::class,
            BenarDokumentasiStats::class,
            BenarHakClientStats::class,
            BenarPasienStats::class,
            BenarObatStats::class,
            BenarPendidikanStats::class,
            BenarPengkajianStats::class,
            BenarReaksiMakananStats::class,
            BenarReaksiObatStats::class,
            BenarWaktuStats::class,
        ];
    }
}
