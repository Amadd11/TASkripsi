<?php

namespace App\Livewire\Chart;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\BenarCara;
use App\Models\BenarWaktu;
use App\Models\BenarDokumentasi;

class RekapPrinsipBenarChart extends Component
{
    public $bulan;
    public $tahun;

    public function mount()
    {
        $this->bulan = now()->format('m');
        $this->tahun = now()->format('Y');
    }

    public function render()
    {
        $start = Carbon::create($this->tahun, $this->bulan)->startOfMonth();
        $end = Carbon::create($this->tahun, $this->bulan)->endOfMonth();

        $items = [
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
            // ... sisanya sampai Benar Waktu
            [
                'label' => 'Benar Waktu',
                'model' => BenarWaktu::class,
                'conditions' => ['is_pagi' => 1, 'is_siang' => 1, 'is_sore' => 1, 'is_malam' => 1],
            ],
        ];

        $chartData = collect($items)->map(function ($item) use ($start, $end) {
            $model = $item['model'];
            $conditions = $item['conditions'];

            $total = $model::whereBetween('created_at', [$start, $end])->count();
            $benar = $model::where($conditions)->whereBetween('created_at', [$start, $end])->count();
            $persen = $total > 0 ? round(($benar / $total) * 100) : 0;

            return [
                'label' => $item['label'],
                'value' => $persen,
                'tooltip' => "{$persen}% - {$benar} pasien",
                'color' => match (true) {
                    $persen < 50 => 'rgb(239, 68, 68)',      // merah
                    $persen < 80 => 'rgb(234, 179, 8)',       // kuning
                    default => 'rgb(34, 197, 94)',            // hijau
                }
            ];
        });

        return view('livewire.chart.rekap-prinsip-benar-chart', [
            'chartData' => $chartData,
        ]);
    }
}
