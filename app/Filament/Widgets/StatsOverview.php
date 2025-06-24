<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\MasterPasien;
use App\Models\FarTransaction;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalPasien = MasterPasien::count();
        $totalTransaksiFarmasi = FarTransaction::count();

        // Ambil total pendapatan bulan ini
        $pendapatanBulanIni = FarTransaction::whereBetween('tgl', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])->sum('grand_total');

        return [
            Stat::make('Total Pasien', $totalPasien)
                ->description('Jumlah pasien terdaftar')
                ->descriptionIcon('heroicon-o-users')
                ->color('info'),

            Stat::make('Total Transaksi Farmasi', $totalTransaksiFarmasi)
                ->description('Total transaksi yang tercatat')
                ->descriptionIcon('heroicon-o-shopping-bag')
                ->color('success'),

            Stat::make('Pendapatan Farmasi Bulan Ini', 'Rp ' . number_format($pendapatanBulanIni, 0, ',', '.'))
                ->description('Pendapatan dari transaksi bulan ini')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('warning'),
        ];
    }
}
