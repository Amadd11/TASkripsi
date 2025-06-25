<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\Widget;
use App\Models\BenarReaksiObat;
use App\Filament\Pages\RekapitulasiChartPage;


class BenarReaksiObatStats extends Widget
{
    protected static ?string $heading = 'Benar Reaksi Obat Pada Bulan ini';

    protected static string $view = 'filament.widgets.benar-reaksi-obat-stats';

    public ?string $filter = null;

    public int $total = 0;
    public int $allTrue = 0;
    public float $allTruePct = 0.0;
    public array $monthsOptions = [];

    public function mount(): void
    {
        $this->filter ??= Carbon::now()->format('Y-m');
        $this->updateStats();
        $this->monthsOptions = $this->getMonthsOptions();
    }

    public function updatedFilter(): void
    {
        $this->updateStats();
    }

    protected function updateStats(): void
    {
        $startOfMonth = Carbon::parse($this->filter)->startOfMonth();
        $endOfMonth = Carbon::parse($this->filter)->endOfMonth();

        $query = BenarReaksiObat::query()
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth]);

        $this->total = $query->count();

        if ($this->total === 0) {
            $this->allTrue = 0;
            $this->allTruePct = 0.0;
        } else {
            $this->allTrue = BenarReaksiObat::query()
                ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
                ->where('is_nama_obat', true)
                ->where('is_label', true)
                ->where('is_resep', true)
                ->count();

            $this->allTruePct = ($this->allTrue / $this->total) * 100;
        }
    }

    protected function getMonthsOptions(): array
    {
        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $month = Carbon::now()->subMonths($i);
            $months[$month->format('Y-m')] = $month->translatedFormat('F Y');
        }
        return $months;
    }

    public static function canView(): bool
    {
        return request()->route()?->getName() === RekapitulasiChartPage::getRouteName();
    }
}
