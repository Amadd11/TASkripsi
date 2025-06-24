<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\BenarDokumentasi;
use Filament\Widgets\Widget;

class BenarDokumentasiStats extends Widget
{
    protected static ?int $sort = 6;

    protected static ?string $heading = 'Benar Dokumentasi Pada Bulan Ini';

    protected static string $view = 'filament.widgets.benar-dokumentasi-stats';

    public ?string $filter = null;

    public int $total = 0;
    public int $allTrue = 0;
    public float $allTruePct = 0.0;
    public array $monthsOptions = [];

    public function mount(): void
    {
        $this->filter ??= Carbon::now()->format('Y-m');
        $this->monthsOptions = $this->getMonthsOptions();
        $this->updateStats();
    }

    public function updatedFilter(): void
    {
        $this->updateStats();
    }

    protected function updateStats(): void
    {
        $startOfMonth = Carbon::parse($this->filter)->startOfMonth();
        $endOfMonth = Carbon::parse($this->filter)->endOfMonth();

        $query = BenarDokumentasi::query()
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth]);

        $this->total = $query->count();

        if ($this->total === 0) {
            $this->allTrue = 0;
            $this->allTruePct = 0.0;
        } else {
            $this->allTrue = BenarDokumentasi::query()
                ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
                ->where('is_pasien', true)
                ->where('is_dosis', true)
                ->where('is_obat', true)
                ->where('is_waktu', true)
                ->where('is_rute', true)
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
