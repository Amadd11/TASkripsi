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

        // Pendapatan dari transaksi tahun ini
        $pendapatanTahunIni = FarTransaction::whereBetween('tgl', [
            Carbon::now()->startOfYear(),
            Carbon::now()->endOfYear()
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

            Stat::make('Pendapatan Farmasi Tahun Ini', 'Rp ' . number_format($pendapatanTahunIni, 0, ',', '.'))
                ->description('Total pendapatan transaksi tahun berjalan')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('warning'),
        ];
    }
}
