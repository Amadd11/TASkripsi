<?php

namespace App\Providers\Filament;

use App\Filament\Pages\IsiCeklist12Benar;
use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Pages\Dashboard;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use App\Filament\Pages\TentangAplikasi;
use App\Filament\Resources\UserResource;
use Filament\Navigation\NavigationGroup;
use Filament\Http\Middleware\Authenticate;
use Filament\Navigation\NavigationBuilder;
use App\Filament\Pages\RekapitulasiChartPage;
use App\Filament\Resources\BenarCaraResource;
use App\Filament\Resources\BenarObatResource;
use App\Filament\Resources\BenarDosisResource;
use App\Filament\Resources\BenarWaktuResource;
use App\Filament\Resources\BenarPasienResource;
use Illuminate\Session\Middleware\StartSession;
use App\Filament\Resources\MasterPasienResource;
use Illuminate\Cookie\Middleware\EncryptCookies;
use App\Filament\Resources\BenarEvaluasiResource;
use Filament\Http\Middleware\AuthenticateSession;
use App\Filament\Resources\BenarHakClientResource;
use App\Filament\Resources\FarTransactionResource;
use App\Filament\Resources\BenarPendidikanResource;
use App\Filament\Resources\BenarPengkajianResource;
use App\Filament\Resources\BenarReaksiObatResource;
use App\Filament\Resources\BenarDokumentasiResource;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Illuminate\Routing\Middleware\SubstituteBindings;
use App\Filament\Resources\BenarReaksiMakananResource;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use BezhanSalleh\FilamentShield\Resources\RoleResource;
use App\Filament\Resources\PemeriksaanKepatuhanResource;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Green,
            ])
            ->brandLogo(fn() => view('components.brand-logo'))
            ->favicon(asset('images/logo-umy.png'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder->groups([
                    // Item tanpa grup akan muncul di paling atas
                    NavigationGroup::make()
                        ->items([
                            ...Dashboard::getNavigationItems(),
                            ...RekapitulasiChartPage::getNavigationItems(),
                            ...FarTransactionResource::getNavigationItems(),
                            ...MasterPasienResource::getNavigationItems(),
                            ...IsiCeklist12Benar::getNavigationItems()
                        ]),


                    // Grup untuk semua prinsip 12 Benar
                    NavigationGroup::make('Prinsip 12 Benar')
                        ->items([
                            ...BenarPasienResource::getNavigationItems(),
                            ...BenarObatResource::getNavigationItems(),
                            ...BenarDosisResource::getNavigationItems(),
                            ...BenarCaraResource::getNavigationItems(),
                            ...BenarWaktuResource::getNavigationItems(),
                            ...BenarDokumentasiResource::getNavigationItems(),
                            ...BenarEvaluasiResource::getNavigationItems(),
                            ...BenarPengkajianResource::getNavigationItems(),
                            ...BenarReaksiObatResource::getNavigationItems(),
                            ...BenarReaksiMakananResource::getNavigationItems(),
                            ...BenarHakClientResource::getNavigationItems(),
                            ...BenarPendidikanResource::getNavigationItems(),
                        ]),

                    // Grup untuk manajemen user, diletakkan di bawah
                    NavigationGroup::make('User Manajemen')
                        ->items([
                            ...UserResource::getNavigationItems(),
                            ...RoleResource::getNavigationItems(),

                        ]),

                    // Grup untuk halaman statis seperti "Tentang"
                    NavigationGroup::make('Bantuan')
                        ->items([
                            ...TentangAplikasi::getNavigationItems(),
                        ]),
                ]);
            });
    }
}
