<?php

namespace App\Filament\Widgets\PersentaseKepatuhan;

use Carbon\Carbon;
use Filament\Widgets\Widget;
use App\Models\BenarEvaluasi;
use App\Filament\Resources\BenarEvaluasiResource\Pages\ListBenarEvaluasis;

class BenarEvaluasiPercentageStats extends Widget
{
    // Tentukan file view yang akan digunakan
    protected static string $view = 'filament.pages.benar-evaluasi-percentage-stats';
    protected int | string | array $columnSpan = 'full';

    public ?string $filter = null;

    // Properti untuk menyefek_terapipan data yang akan ditampilkan di view
    public int $total = 0;
    public int $is_alergi = 0;
    public int $is_efek_samping = 0;
    public int $is_efek_terapi = 0;
    public float $alergi_percent = 0.0;
    public float $efek_samping_percent = 0.0;
    public float $efek_terapi_percent = 0.0;
    public array $monthsOptions = [];

    /**
     * Dijalankan saat widget pertama kali defek_terapiuat.
     */
    public function mount(): void
    {
        // Set filter default ke bulan ini jika belum ada
        $this->filter = request()->query('bulan', now()->format('Y-m'));

        // Siapkan data awal
        $this->monthsOptions = $this->getMonthsOptions();
        $this->calculateStats();
    }

    /**
     * Dijalankan setiap kali nilai $this->filter berubah (saat user memilih bulan lain).
     */
    public function updatedFilter(): void
    {
        $this->calculateStats();
    }

    /**
     * Logika utama untuk menghitung statistik.
     */
    protected function calculateStats(): void
    {
        // Ambil tahun dan bulan dari filter
        $year = Carbon::parse($this->filter)->year;
        $month = Carbon::parse($this->filter)->month;

        // Query dasar yang memfilter berdasarkan bulan dan tahun dari kolom 'tanggal'
        $query = BenarEvaluasi::query()
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month);

        $this->total = $query->count();
        $this->is_efek_terapi = (clone $query)->where('is_efek_terapi', true)->count();
        $this->is_efek_samping = (clone $query)->where('is_efek_samping', true)->count();
        $this->is_alergi = (clone $query)->where('is_alergi', true)->count();


        $this->alergi_percent = $this->total ? round(($this->is_alergi / $this->total) * 100, 1) : 0;
        $this->efek_samping_percent = $this->total ? round(($this->is_efek_samping / $this->total) * 100, 1) : 0;
        $this->efek_terapi_percent = $this->total ? round(($this->is_efek_terapi / $this->total) * 100, 1) : 0;
    }

    /**
     * Membuat daftar pilihan bulan dari Januari hingga Desember untuk tahun ini.
     */
    protected function getMonthsOptions(): array
    {
        return collect(range(1, 12))->mapWithKeys(function ($month) {
            $date = Carbon::create(date('Y'), $month, 1);
            return [$date->format('Y-m') => $date->translatedFormat('F')];
        })->toArray();
    }


    public static function canView(): bool
    {
        return request()->route()?->getName() === ListBenarEvaluasis::getRouteName();
    }
}
