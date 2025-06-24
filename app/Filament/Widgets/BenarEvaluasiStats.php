<?php

namespace App\Filament\Widgets;

use App\Models\BenarEvaluasi;
use Carbon\Carbon;
use Filament\Widgets\Widget;


class BenarEvaluasiStats extends Widget
{
    protected static ?int $sort = 6;
    protected static ?string $heading = 'Benar Evaluasi Pada Bulan ini';

    protected static string $view = 'filament.widgets.benar-evaluasi-stats';

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

        $query = BenarEvaluasi::query()
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth]);

        $this->total = $query->count();

        if ($this->total === 0) {
            $this->allTrue = 0;
            $this->allTruePct = 0.0;
        } else {
            $this->allTrue = BenarEvaluasi::query()
                ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
                ->where('is_efek_terapi', true)
                ->where('is_efek_samping', true)
                ->where('is_alergi', true)
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
}
