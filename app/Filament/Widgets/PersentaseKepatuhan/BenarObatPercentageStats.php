<?php

namespace App\Filament\Widgets\PersentaseKepatuhan;

use App\Filament\Resources\BenarObatResource\Pages\ListBenarObats;
use Carbon\Carbon;
use App\Models\BenarObat;
use Filament\Widgets\Widget;

class BenarObatPercentageStats extends Widget
{
    // Tentukan file view yang akan digunakan
    protected static string $view = 'filament.pages.benar-obat-percentage-stats';
    protected int | string | array $columnSpan = 'full';

    // Properti untuk menyjumlahpan nilai filter yang dipilih
    public ?string $filter = null;

    // Properti untuk menyjumlahpan data yang akan ditampilkan di view
    public int $total = 0;
    public int $is_nama_obat = 0;
    public int $is_label = 0;
    public int $is_resep = 0;
    public float $nama_obat_percent = 0.0;
    public float $label_percent = 0.0;
    public float $resep_percent = 0.0;
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
        $query = BenarObat::query()
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month);

        $this->total = $query->count();
        $this->is_nama_obat = (clone $query)->where('is_nama_obat', true)->count();
        $this->is_label = (clone $query)->where('is_label', true)->count();
        $this->is_resep = (clone $query)->where('is_resep', true)->count();

        $this->nama_obat_percent = $this->total ? round(($this->is_nama_obat / $this->total) * 100, 1) : 0;
        $this->label_percent = $this->total ? round(($this->is_label / $this->total) * 100, 1) : 0;
        $this->resep_percent = $this->total ? round(($this->is_resep / $this->total) * 100, 1) : 0;
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
        return request()->route()?->getName() === ListBenarObats::getRouteName();
    }
}
