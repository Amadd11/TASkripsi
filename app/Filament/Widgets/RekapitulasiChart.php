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
    protected static ?string $heading = 'Rekapitulasi Kepatuhan 12 Benar (Tahun Terpilih)';

    protected static ?int $sort = 11;
    protected static ?string $maxHeight = '300px';


    public ?string $filter = null;

    public static function canView(): bool
    {
        return request()->route()?->getName() === RekapitulasiChartPage::getRouteName();
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getFilters(): ?array
    {
        $years = [];
        for ($i = 0; $i < 5; $i++) {
            $year = now()->subYears($i)->format('Y');
            $years[$year] = $year;
        }
        return $years;
    }

    protected function getData(): array
    {
        $year = $this->filter ?? now()->format('Y');
        $start = Carbon::parse("$year-01-01")->startOfYear();
        $end = Carbon::parse("$year-12-31")->endOfYear();

        $calculatePercentage = fn($true, $total) => $total > 0 ? round(($true / $total) * 100, 2) : 0;

        $categories = [
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
        // PERUBAHAN 1: Buat array untuk menyimpan jumlah data yang patuh
        $compliantCounts = [];

        foreach ($categories as $category) {
            $labels[] = $category['label'];

            $query = $category['model']::whereBetween('tanggal', [$start, $end]);
            $total = $query->count();

            $compliantQuery = clone $query;
            foreach ($category['conditions'] as $field => $value) {
                $compliantQuery->where($field, $value);
            }
            $compliant = $compliantQuery->count();
            $percentage = $calculatePercentage($compliant, $total);

            $data[] = $percentage;
            // PERUBAHAN 2: Simpan jumlah data yang patuh
            $compliantCounts[] = $compliant;

            // Logika pewarnaan sudah benar sesuai permintaan Anda
            $colors[] = match (true) {
                $percentage >= 80 => '#10B981',   // Hijau: 80% - 100%
                $percentage >= 50 => '#FACC15',   // Kuning: 50% - 79%
                default            => '#EF4444',   // Merah: < 50%
            };
        }

        // PERUBAHAN 3: Encode array jumlah data ke format JSON untuk digunakan di JavaScript
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
            'options' => [
                'responsive' => true,
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                        'max' => 100,
                    ],
                    'x' => [
                        'ticks' => [
                            'autoSkip' => false,
                        ],
                    ],
                ],
                'plugins' => [
                    'tooltip' => [
                        'callbacks' => [
                            // PERUBAHAN 4: Modifikasi fungsi callback untuk tooltip
                            'label' => "function(context) {
                                const compliantData = {$compliantCountsJson};
                                const count = compliantData[context.dataIndex];
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y + '% - ' + count + ' Pasien';
                                }
                                return label;
                            }",
                        ],
                    ],
                ],
            ],
        ];
    }
    public function getColumnSpan(): int | string | array
    {
        return 'full';
    }
}
