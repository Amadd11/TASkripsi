<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\BenarCara;
use Filament\Widgets\Widget; // Ubah dari StatsOverviewWidget menjadi Widget

class BenarCaraStats extends Widget // Ubah BaseWidget menjadi Widget
{
    protected static ?int $sort = 6;
    protected static string $heading = 'Benar Cara Pada Bulan ini';

    // Definisikan view kustom untuk widget ini
    protected static string $view = 'filament.widgets.benar-cara-stats';

    // Properti publik untuk filter
    public ?string $filter = null; // Akan menyimpan nilai filter (e.g., '2023-01')

    // Properti publik untuk menyimpan hasil statistik
    public int $total = 0;
    public int $allTrue = 0;
    public float $allTruePct = 0.0;
    public array $monthsOptions = []; // Opsi bulan untuk dropdown filter

    public function mount(): void
    {
        // Set nilai default filter ke bulan saat ini jika belum ada
        if (is_null($this->filter)) {
            $this->filter = Carbon::now()->format('Y-m');
        }
        $this->updateStats(); // Panggil updateStats saat pertama kali dimuat
        $this->monthsOptions = $this->getMonthsOptions(); // Isi opsi bulan
    }

    public function updatedFilter(): void
    {
        $this->updateStats();
    }

    // Metode untuk menghitung dan memperbarui statistik
    protected function updateStats(): void
    {
        // Tentukan rentang tanggal berdasarkan filter yang dipilih
        if ($this->filter) {
            $startOfMonth = Carbon::parse($this->filter)->startOfMonth();
            $endOfMonth = Carbon::parse($this->filter)->endOfMonth();
        } else {
            // Default: bulan ini jika tidak ada filter yang dipilih
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();
        }

        // Lakukan query data dengan filter tanggal
        $query = BenarCara::query()
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth]); // Filter berdasarkan kolom 'tanggal'

        $this->total = $query->count();

        if ($this->total === 0) {
            $this->allTrue = 0;
            $this->allTruePct = 0.0;
        } else {
            // Kloning query agar whereBetween tidak diaplikasikan dua kali pada builder yang sama
            $allTrueQuery = BenarCara::query()
                ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
                ->where('is_oral', true)
                ->where('is_iv', true)
                ->where('is_im', true)
                ->count();

            $this->allTruePct = ($this->allTrue / $this->total) * 100;
        }
    }

    // Metode untuk mendapatkan opsi bulan/tahun (seperti getFilters sebelumnya)
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
