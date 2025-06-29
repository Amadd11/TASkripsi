<?php

namespace App\Filament\Widgets\PersentaseKepatuhan;

use App\Filament\Resources\BenarPasienResource\Pages\ListBenarPasiens;
use App\Models\BenarPasien;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class BenarPasienPercentageStats extends Widget
{
    // Tentukan file view yang akan digunakan
    protected static string $view = 'filament.pages.benar-pasien-percentage-stats';
    protected int | string | array $columnSpan = 'full';

    // Properti untuk menyjumlahpan nilai filter yang dipilih
    public ?string $filter = null;

    // Properti untuk menyjumlahpan data yang akan ditampilkan di view
    public int $total = 0;
    public int $is_nama = 0;
    public int $is_tgl_lahir = 0;
    public float $nama_percent = 0.0;
    public float $tgl_lahir_percent = 0.0;
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
        $query = BenarPasien::query()
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month);

        $this->total = $query->count();
        $this->is_nama = (clone $query)->where('is_nama', true)->count();
        $this->is_tgl_lahir = (clone $query)->where('is_tgl_lahir', true)->count();

        $this->nama_percent = $this->total ? round(($this->is_nama / $this->total) * 100, 1) : 0;
        $this->tgl_lahir_percent = $this->total ? round(($this->is_tgl_lahir / $this->total) * 100, 1) : 0;
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
        return request()->route()?->getName() === ListBenarPasiens::getRouteName();
    }
}
