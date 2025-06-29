<?php

namespace App\Filament\Widgets\PersentaseKepatuhan;

use Carbon\Carbon;
use App\Filament\Resources\BenarWaktuResource\Pages\ListBenarWaktus;
use App\Models\BenarWaktu;
use Filament\Widgets\Widget;

class BenarWaktuPercentageStats extends Widget
{
    // Tentukan file view yang akan digunakan
    protected static string $view = 'filament.pages.benar-waktu-percentage-stats';
    protected int | string | array $columnSpan = 'full';

    // Properti untuk menyjumlahpan nilai filter yang dipilih
    public ?string $filter = null;

    // Properti untuk menyjumlahpan data yang akan ditampilkan di view
    public int $total = 0;
    public int $is_pagi = 0;
    public int $is_siang = 0;
    public int $is_sore = 0;
    public int $is_malam = 0;
    public float $pagi_percent = 0.0;
    public float $siang_percent = 0.0;
    public float $sore_percent = 0.0;
    public float $malam_percent = 0.0;
    public array $monthsOptions = [];

    /**
     * Dijalankan saat widget pertama kali djumlahuat.
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
        $query = BenarWaktu::query()
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month);

        $this->total = $query->count();
        $this->is_pagi = (clone $query)->where('is_pagi', true)->count();
        $this->is_siang = (clone $query)->where('is_siang', true)->count();
        $this->is_sore = (clone $query)->where('is_sore', true)->count();
        $this->is_malam = (clone $query)->where('is_malam', true)->count();

        $this->pagi_percent = $this->total ? round(($this->is_pagi / $this->total) * 100, 1) : 0;
        $this->siang_percent = $this->total ? round(($this->is_siang / $this->total) * 100, 1) : 0;
        $this->sore_percent = $this->total ? round(($this->is_sore / $this->total) * 100, 1) : 0;
        $this->malam_percent = $this->total ? round(($this->is_malam / $this->total) * 100, 1) : 0;
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
        return request()->route()?->getName() === ListBenarWaktus::getRouteName();
    }
}
