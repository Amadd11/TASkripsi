<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\MasterPasien;
use Filament\Widgets\ChartWidget;

class PasienVisitChart extends ChartWidget
{
    protected static ?string $heading = 'Jumlah Kunjungan Pasien Dalam Satu Tahun';
    protected static ?int $sort = 1;
    protected static ?string $maxHeight = '300px';

    public ?string $filter = null;

    protected function getFilters(): ?array
    {
        $years = [];
        for ($i = 0; $i < 5; $i++) {
            $year = Carbon::now()->subYears($i);
            $years[$year->format('Y')] = $year->format('Y');
        }

        return $years;
    }

    protected function getData(): array
    {
        $selectedYear = $this->filter ?? Carbon::now()->year;

        $startOfYear = Carbon::parse($selectedYear . '-01-01')->startOfYear();
        $endOfYear = Carbon::parse($selectedYear . '-12-31')->endOfYear();

        $data = MasterPasien::selectRaw('MONTH(tgl_kunj) as month, COUNT(*) as count')
            ->whereBetween('tgl_kunj', [$startOfYear, $endOfYear])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $labels = [];
        $counts = [];
        $backgroundColors = [
            'rgba(255, 99, 132, 0.2)',
            'rgba(255, 159, 64, 0.2)',
            'rgba(255, 205, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(201, 203, 207, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 159, 64, 0.2)',
            'rgba(255, 99, 132, 0.2)',
        ];

        for ($i = 1; $i <= 12; $i++) {
            $labels[] = Carbon::create()->month($i)->translatedFormat('F');
            $counts[$i] = 0;
        }

        foreach ($data as $item) {
            $monthNumber = (int) $item->month;
            $counts[$monthNumber] = $item->count;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Jumlah Kunjungan',
                    'data' => array_values($counts),
                    'backgroundColor' => $backgroundColors,
                    // Tidak pakai borderColor atau borderWidth
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    public function getColumnSpan(): int | string | array
    {
        return 'full';
    }
}
