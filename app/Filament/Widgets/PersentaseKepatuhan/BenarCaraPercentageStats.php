<?php

namespace App\Filament\Widgets\PersentaseKepatuhan;

use Carbon\Carbon;
use App\Models\BenarCara;
use Filament\Widgets\Widget;
use App\Filament\Resources\BenarCaraResource\Pages\ListBenarCaras;

class BenarCaraPercentageStats extends Widget
{
    // Tentukan file view yang akan digunakan
    protected static string $view = 'filament.pages.benar-cara-percentage-stats';
    protected int | string | array $columnSpan = 'full';

    // Properti untuk menyimpan nilai filter yang dipilih
    public ?string $filter = null;

    // Properti untuk menyimpan data yang akan ditampilkan di view
    public int $total = 0;
    public int $is_oral = 0;
    public int $is_iv = 0;
    public int $is_im = 0;
    public float $oral_percent = 0.0;
    public float $iv_percent = 0.0;
    public float $im_percent = 0.0;
    public array $monthsOptions = [];

    /**
     * Dijalankan saat widget pertama kali dimuat.
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
        $query = BenarCara::query()
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month);

        $this->total = $query->count();
        $this->is_oral = (clone $query)->where('is_oral', true)->count();
        $this->is_iv = (clone $query)->where('is_iv', true)->count();
        $this->is_im = (clone $query)->where('is_im', true)->count();

        $this->oral_percent = $this->total ? round(($this->is_oral / $this->total) * 100, 1) : 0;
        $this->iv_percent = $this->total ? round(($this->is_iv / $this->total) * 100, 1) : 0;
        $this->im_percent = $this->total ? round(($this->is_im / $this->total) * 100, 1) : 0;
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
        return request()->route()?->getName() === ListBenarCaras::getRouteName();
    }
}
