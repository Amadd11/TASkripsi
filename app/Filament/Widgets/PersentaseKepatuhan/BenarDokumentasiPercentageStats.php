<?php

namespace App\Filament\Widgets\PersentaseKepatuhan;

use Carbon\Carbon;
use Filament\Widgets\Widget;
use App\Models\BenarDokumentasi;
use App\Filament\Resources\BenarDokumentasiResource\Pages\ListBenarDokumentasis;

class BenarDokumentasiPercentageStats extends Widget
{
    protected static string $view = 'filament.pages.benar-dokumentasi-percentage-stats';
    protected int | string | array $columnSpan = 'full';

    public ?string $filter = null;
    public array $monthsOptions = [];

    // Properti baru untuk menyimpan data
    public int $total = 0;
    public int $is_pasien = 0;
    public int $is_dosis = 0;
    public int $is_obat = 0;
    public int $is_waktu = 0;
    public int $is_rute = 0;

    public float $pasien_percent = 0.0;
    public float $dosis_percent = 0.0;
    public float $obat_percent = 0.0;
    public float $waktu_percent = 0.0;
    public float $rute_percent = 0.0;

    public function mount(): void
    {
        $this->filter = request()->query('bulan', now()->format('Y-m'));
        $this->monthsOptions = $this->getMonthsOptions();
        $this->calculateStats();
    }

    public function updatedFilter(): void
    {
        $this->calculateStats();
    }

    protected function calculateStats(): void
    {
        $year = Carbon::parse($this->filter)->year;
        $month = Carbon::parse($this->filter)->month;

        // Pastikan model dan kolom tanggal sudah benar
        $query = BenarDokumentasi::query()
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month);

        $this->total = $query->count();

        // Hitung data sesuai kolom baru
        $this->is_pasien = (clone $query)->where('is_pasien', true)->count();
        $this->is_dosis = (clone $query)->where('is_dosis', true)->count();
        $this->is_obat = (clone $query)->where('is_obat', true)->count();
        $this->is_waktu = (clone $query)->where('is_waktu', true)->count();
        $this->is_rute = (clone $query)->where('is_rute', true)->count();

        // Hitung persentase baru
        $this->pasien_percent = $this->total ? round(($this->is_pasien / $this->total) * 100, 1) : 0;
        $this->dosis_percent = $this->total ? round(($this->is_dosis / $this->total) * 100, 1) : 0;
        $this->obat_percent = $this->total ? round(($this->is_obat / $this->total) * 100, 1) : 0;
        $this->waktu_percent = $this->total ? round(($this->is_waktu / $this->total) * 100, 1) : 0;
        $this->rute_percent = $this->total ? round(($this->is_rute / $this->total) * 100, 1) : 0;
    }

    protected function getMonthsOptions(): array
    {
        return collect(range(1, 12))->mapWithKeys(function ($month) {
            $date = Carbon::create(date('Y'), $month, 1);
            return [$date->format('Y-m') => $date->translatedFormat('F')];
        })->toArray();
    }

    public static function canView(): bool
    {
        return request()->route()?->getName() === ListBenarDokumentasis::getRouteName();
    }
}
