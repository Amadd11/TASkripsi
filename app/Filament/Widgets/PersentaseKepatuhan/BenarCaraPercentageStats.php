<?php

namespace App\Filament\Widgets\PersentaseKepatuhan;

use App\Filament\Resources\BenarCaraResource\Pages\ListBenarCaras;
use App\Models\BenarCara;
use Filament\Widgets\Widget;

class BenarCaraPercentageStats extends Widget
{
    protected static string $view = 'filament.pages.benar-cara-percentage-stats';

    protected int | string | array $columnSpan = 'full';

    public function getData(): array
    {
        $total = BenarCara::count();

        $oral = BenarCara::where('is_oral', true)->count();
        $iv = BenarCara::where('is_iv', true)->count();
        $im = BenarCara::where('is_im', true)->count();

        return [
            'total' => $total,
            'is_oral' => $oral,
            'is_iv' => $iv,
            'is_im' => $im,
            'oral_percent' => $total ? round($oral / $total * 100, 1) : 0,
            'iv_percent' => $total ? round($iv / $total * 100, 1) : 0,
            'im_percent' => $total ? round($im / $total * 100, 1) : 0,
        ];
    }

    public static function canView(): bool
    {
        return request()->route()?->getName() === ListBenarCaras::getRouteName();
    }
}
