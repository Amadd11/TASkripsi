<?php

namespace App\Filament\Widgets\PersentaseKepatuhan;

use Carbon\Carbon;
use App\Filament\Resources\BenarHakClientResource\Pages\ListBenarHakClients;
use App\Models\BenarHakClient;
use Filament\Widgets\Widget;

class BenarHakClientPercentageStats extends Widget
{
    // Tentukan file view yang akan digunakan
    protected static string $view = 'filament.pages.benar-hak-client-percentage-stats';
    protected int | string | array $columnSpan = 'full';

    // Properti untuk menyjumlahpan nilai filter yang dipilih
    public ?string $filter = null;

    // Properti untuk menyjumlahpan data yang akan ditampilkan di view
    public int $total = 0;
    public int $is_ic = 0;
    public float $ic_percent = 0.0;
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
        $query = BenarHakClient::query()
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month);

        $this->total = $query->count();
        $this->is_ic = (clone $query)->where('is_ic', true)->count();

        $this->ic_percent = $this->total ? round(($this->is_ic / $this->total) * 100, 1) : 0;
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
        return request()->route()?->getName() === ListBenarHakClients::getRouteName();
    }
}
