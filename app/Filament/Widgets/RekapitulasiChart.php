<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

use App\Models\BenarCara;
use App\Models\BenarDokumentasi;
use App\Models\BenarDosis;
use App\Models\BenarEvaluasi;
use App\Models\BenarHakClient;
use App\Models\BenarObat;
use App\Models\BenarPasien;
use App\Models\BenarPendidikan;
use App\Models\BenarPengkajian;
use App\Models\BenarReaksiMakanan;
use App\Models\BenarReaksiObat;
use App\Models\BenarWaktu;
use App\Filament\Pages\RekapitulasiChartPage;

class RekapitulasiChart extends ChartWidget
{
    // Heading sekarang diatur secara dinamis, jadi kita komentari ini.
    // protected static ?string $heading = 'Rekapitulasi Kepatuhan 12 Benar (Tahun Terpilih)';

    protected static ?int $sort = 11;
    protected static ?string $maxHeight = '300px';

    // Properti untuk filter. Formatnya adalah 'Y-m'.
    public ?string $filter = null;

    public function getColumnSpan(): int | string | array
    {
        return 'full';
    }
    
    public static function canView(): bool
    {
        return request()->route()?->getName() === RekapitulasiChartPage::getRouteName();
    }

    public function mount(): void
    {
        // Atur filter default ke bulan ini saat widget dimuat
        $this->filter = now()->format('Y-m');
    }

    public function getHeading(): ?string
    {
        if (!$this->filter) {
            return null;
        }

        $selectedDate = Carbon::createFromFormat('Y-m', $this->filter);
        return 'Rekapitulasi Kepatuhan 12 Benar - ' . $selectedDate->translatedFormat('F Y');
    }

    protected function getType(): string
    {
        return 'bar';
    }

    /**
     * DIUBAH: Filter sekarang menghasilkan daftar bulan, bukan tahun.
     */
    protected function getFilters(): ?array
    {
        $months = [];
        for ($i = 0; $i < 12; $i++) { // Tampilkan 12 bulan terakhir
            $date = now()->subMonths($i);
            $months[$date->format('Y-m')] = $date->translatedFormat('F Y');
        }
        return $months;
    }

    protected function getData(): array
    {
        // DIUBAH: Gunakan filter bulan untuk menentukan rentang tanggal
        $yearMonth = $this->filter ?? now()->format('Y-m');
        $selectedDate = Carbon::createFromFormat('Y-m', $yearMonth);

        $start = $selectedDate->copy()->startOfMonth();
        $end = $selectedDate->copy()->endOfMonth();

        $calculatePercentage = fn($true, $total) => $total > 0 ? round(($true / $total) * 100, 2) : 0;

        $categories = [
            // ... (array kategori Anda tidak perlu diubah)
            [
                'label' => 'Benar Cara',
                'model' => BenarCara::class,
                'conditions' => ['is_oral' => 1, 'is_iv' => 1, 'is_im' => 1],
            ],
            [
                'label' => 'Benar Dokumentasi',
                'model' => BenarDokumentasi::class,
                'conditions' => ['is_pasien' => 1, 'is_dosis' => 1, 'is_obat' => 1, 'is_waktu' => 1, 'is_rute' => 1],
            ],
            [
                'label' => 'Benar Dosis',
                'model' => BenarDosis::class,
                'conditions' => ['is_jumlah' => 1, 'is_potensi' => 1],
            ],
            [
                'label' => 'Benar Evaluasi',
                'model' => BenarEvaluasi::class,
                'conditions' => ['is_efek_samping' => 1, 'is_alergi' => 1, 'is_efek_terapi' => 1],
            ],
            [
                'label' => 'Benar Hak Klien',
                'model' => BenarHakClient::class,
                'conditions' => ['is_ic' => 1],
            ],
            [
                'label' => 'Benar Pasien',
                'model' => BenarPasien::class,
                'conditions' => ['is_nama' => 1, 'is_tgl_lahir' => 1],
            ],
            [
                'label' => 'Benar Obat',
                'model' => BenarObat::class,
                'conditions' => ['is_nama_obat' => 1, 'is_label' => 1, 'is_resep' => 1],
            ],
            [
                'label' => 'Benar Pendidikan',
                'model' => BenarPendidikan::class,
                'conditions' => ['is_edukasi' => 1],
            ],
            [
                'label' => 'Benar Pengkajian',
                'model' => BenarPengkajian::class,
                'conditions' => ['is_suhu' => 1, 'is_tensi' => 1],
            ],
            [
                'label' => 'Benar Reaksi Makanan',
                'model' => BenarReaksiMakanan::class,
                'conditions' => ['is_efek_makanan' => 1],
            ],
            [
                'label' => 'Benar Reaksi Obat',
                'model' => BenarReaksiObat::class,
                'conditions' => ['is_efek_samping' => 1, 'is_alergi' => 1, 'is_efek_terapi' => 1],
            ],
            [
                'label' => 'Benar Waktu',
                'model' => BenarWaktu::class,
                'conditions' => ['is_pagi' => 1, 'is_siang' => 1, 'is_sore' => 1, 'is_malam' => 1],
            ],
        ];

        $labels = [];
        $data = [];
        $colors = [];
        $compliantCounts = [];

        foreach ($categories as $category) {
            $labels[] = $category['label'];

            // Query akan menggunakan rentang tanggal bulanan dari variabel $start dan $end
            $query = $category['model']::whereBetween('tanggal', [$start, $end]);
            $total = $query->count();

            $compliantQuery = clone $query;
            foreach ($category['conditions'] as $field => $value) {
                $compliantQuery->where($field, $value);
            }
            $compliant = $compliantQuery->count();
            $percentage = $calculatePercentage($compliant, $total);

            $data[] = $percentage;
            $compliantCounts[] = $compliant;

            $colors[] = match (true) {
                $percentage >= 80 => '#10B981',   // Hijau
                $percentage >= 50 => '#FACC15',   // Kuning
                default            => '#EF4444',   // Merah
            };
        }

        $compliantCountsJson = json_encode($compliantCounts);

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Persentase Kepatuhan (%)',
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderColor' => $colors,
                    'borderWidth' => 1,
                ],
            ],
            'options' => [ // Bagian options tetap sama
                'responsive' => true,
                'scales' => [
                    'y' => ['beginAtZero' => true, 'max' => 100],
                    'x' => ['ticks' => ['autoSkip' => false]],
                ],
                'plugins' => [
                    'tooltip' => [
                        'callbacks' => [
                            'label' => "function(context) {
                                const compliantData = {$compliantCountsJson};
                                const count = compliantData[context.dataIndex];
                                let label = context.dataset.label || '';
                                if (label) { label += ': '; }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y + '% (' + count + ' Pasien Patuh)';
                                }
                                return label;
                            }",
                        ],
                    ],
                ],
            ],
        ];
    }
}
