<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\BenarCara;
use Filament\Widgets\Widget;
use App\Filament\Pages\RekapitulasiChartPage;

class BenarCaraStats extends Widget
{

    protected static ?string $heading = 'Benar Cara Pada Bulan Ini';

    protected static string $view = 'filament.widgets.benar-cara-stats';

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

        $query = BenarCara::query()
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth]);

        $this->total = $query->count();

        if ($this->total === 0) {
            $this->allTrue = 0;
            $this->allTruePct = 0.0;
        } else {
            $this->allTrue = BenarCara::query()
                ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
                ->where(function ($query) {
                    $query->where('is_oral', true)
                        ->orWhere('is_iv', true)
                        ->orWhere('is_im', true)
                        ->orWhere('is_intratekal', true)
                        ->orWhere('is_subkutan', true)
                        ->orWhere('is_sublingual', true)
                        ->orWhere('is_rektal', true)
                        ->orWhere('is_vaginal', true)
                        ->orWhere('is_okular', true)
                        ->orWhere('is_otik', true)
                        ->orWhere('is_nasal', true)
                        ->orWhere('is_nebulisasi', true)
                        ->orWhere('is_topikal', true);
                })
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
