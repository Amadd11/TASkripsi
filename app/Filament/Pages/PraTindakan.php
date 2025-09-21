<?php

namespace App\Filament\Pages;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Models\Pemeriksaan;
use App\Models\MasterPasien;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use App\Filament\Resources\PemeriksaanResource;

class PraTindakan extends Page implements HasForms
{
    use HasPageShield;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static string $view = 'filament.pages.pra-tindakan';
    protected static ?string $title = 'Form Pra Tindakan';
    protected static ?string $navigationGroup = 'Pemeriksaan';
    protected static ?string $slug = 'pra-tindakan';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pasien')
                    ->description('Pilih pasien yang akan dilakukan pemeriksaan checklist.')
                    ->schema([
                        Forms\Components\Select::make('no_cm')
                            ->label('Nomor Rekam Medis (No. CM)')
                            ->options(
                                MasterPasien::all()->mapWithKeys(function ($pasien) {
                                    return [$pasien->no_cm => "{$pasien->no_cm} - {$pasien->nama_pas}"];
                                })
                            )
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if (! $state) {
                                    $set('nama_pas', null);
                                    $set('tgl_lahir', null);
                                    $set('no_reg', null);
                                    return;
                                }
                                $pasien = MasterPasien::where('no_cm', $state)->first();
                                if ($pasien) {
                                    $set('nama_pas', $pasien->nama_pas);
                                    $set('tgl_lahir', Carbon::parse($pasien->tgl_lahir)->translatedFormat('d F Y'));
                                    $set('no_reg', $pasien->no_reg);
                                }
                            }),
                        Forms\Components\TextInput::make('no_reg')
                            ->label('Nomor Registrasi')
                            ->disabled(),
                        Forms\Components\TextInput::make('nama_pas')
                            ->label('Nama Pasien')
                            ->dehydrated(false)
                            ->disabled(),
                        Forms\Components\TextInput::make('tgl_lahir')
                            ->label('Tanggal Lahir')
                            ->dehydrated(false)
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('Pra Tindakan: Verifikasi Sebelum Pemberian Obat')
                    ->schema([
                        Forms\Components\Section::make('1. Benar Pasien')
                            ->icon('heroicon-o-user-circle')->collapsible()->schema([
                                Forms\Components\Toggle::make('pasien_check_all')
                                    ->label('Pilih Semua')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        $set('pasien_is_nama', $state);
                                        $set('pasien_is_tgl_lahir', $state);
                                    }),
                                Forms\Components\Fieldset::make('Verifikasi Identitas Pasien')->schema([
                                    Forms\Components\Toggle::make('pasien_is_nama')
                                        ->label('Apakah Nama Pasien Benar?')
                                        ->hint('Centang jika nama pasien sudah sesuai.')
                                        ->required(),
                                    Forms\Components\Toggle::make('pasien_is_tgl_lahir')
                                        ->label('Apakah Tanggal Lahir Benar?')
                                        ->hint('Centang jika tanggal lahir pasien sudah sesuai.')
                                        ->required(),
                                ])->columns(1),
                                Forms\Components\Textarea::make('pasien_keterangan')->label('Keterangan Benar Pasien')->rows(2),
                            ]),
                        Forms\Components\Section::make('2. Benar Obat')
                            ->icon('heroicon-o-beaker')->collapsible()->collapsed()->schema([
                                Forms\Components\Toggle::make('obat_check_all')
                                    ->label('Pilih Semua')
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set) {
                                        $set('obat_is_nama_obat', $state);
                                        $set('obat_is_label', $state);
                                        $set('obat_is_resep', $state);
                                    }),
                                Forms\Components\Fieldset::make('Verifikasi Obat')->schema([
                                    Forms\Components\Toggle::make('obat_is_nama_obat')
                                        ->label('Apakah Nama Obat Benar?')
                                        ->hint('Centang jika nama obat yang diberikan sudah sesuai.')
                                        ->required(),
                                    Forms\Components\Toggle::make('obat_is_label')
                                        ->label('Apakah Label Obat Benar?')
                                        ->hint('Centang jika label obat sudah sesuai dengan resep.')
                                        ->required(),
                                    Forms\Components\Toggle::make('obat_is_resep')
                                        ->label('Apakah Resep Benar?')
                                        ->hint('Centang jika resep sudah diverifikasi kebenarannya.')
                                        ->required(),
                                ])->columns(1),
                                Forms\Components\Textarea::make('obat_keterangan')->label('Keterangan')->rows(2),
                            ]),
                        Forms\Components\Section::make('3. Benar Dosis')
                            ->icon('heroicon-o-scale')->collapsible()->collapsed()->schema([
                                Forms\Components\Toggle::make('dosis_check_all')
                                    ->label('Pilih Semua')
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set) {
                                        $set('dosis_is_jumlah', $state);
                                        $set('dosis_is_potensi', $state);
                                    }),
                                Forms\Components\Fieldset::make('Verifikasi Dosis')->schema([
                                    Forms\Components\Toggle::make('dosis_is_jumlah')
                                        ->label('Apakah Jumlah/Dosis sudah sesuai?')
                                        ->hint('Centang jika jumlah atau dosis yang diberikan sudah benar.')
                                        ->required(),
                                    Forms\Components\Toggle::make('dosis_is_potensi')
                                        ->label('Apakah Potensi/Kekuatan sudah sesuai?')
                                        ->hint('Centang jika potensi atau kekuatan obat sudah sesuai.')
                                        ->required(),
                                ])->columns(1),
                                Forms\Components\Textarea::make('dosis_keterangan')->label('Keterangan')->rows(2),
                            ]),
                        Forms\Components\Section::make('4. Benar Cara')
                            ->icon('heroicon-o-paper-airplane')->collapsible()->collapsed()->schema([
                                Forms\Components\Toggle::make('cara_check_all')
                                    ->label('Pilih Semua')
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set) {
                                        $set('cara_is_oral', $state);
                                        $set('cara_is_iv', $state);
                                        $set('cara_is_im', $state);
                                    }),
                                Forms\Components\Fieldset::make('Verifikasi Cara Pemberian')->schema([
                                    Forms\Components\Toggle::make('cara_is_oral')
                                        ->label('Apakah Pemberian Oral?')
                                        ->hint('Centang jika cara pemberian adalah oral (melalui mulut).')
                                        ->required(),
                                    Forms\Components\Toggle::make('cara_is_iv')
                                        ->label('Apakah Pemberian Intravena (IV)?')
                                        ->hint('Centang jika cara pemberian adalah intravena (melalui pembuluh darah).')
                                        ->required(),
                                    Forms\Components\Toggle::make('cara_is_im')
                                        ->label('Apakah Pemberian Intramuskular (IM)?')
                                        ->hint('Centang jika cara pemberian adalah intramuskular (melalui otot).')
                                        ->required(),
                                ])->columns(1),
                                Forms\Components\Textarea::make('cara_keterangan')->label('Keterangan')->rows(2),
                            ]),
                        Forms\Components\Section::make('5. Benar Waktu')
                            ->icon('heroicon-o-clock')->collapsible()->collapsed()->schema([
                                Forms\Components\Toggle::make('waktu_check_all')
                                    ->label('Pilih Semua')
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set, $state) {
                                        $currentHour = now('Asia/Jakarta')->hour;
                                        if ($currentHour >= 6 && $currentHour < 8) $set('waktu_is_pagi', $state);
                                        if ($currentHour >= 11 && $currentHour < 13) $set('waktu_is_siang', $state);
                                        if ($currentHour >= 16 && $currentHour < 18) $set('waktu_is_sore', $state);
                                        if ($currentHour >= 19 && $currentHour < 22) $set('waktu_is_malam', $state);
                                    })
                                    ->dehydrated(false),
                                Forms\Components\Fieldset::make('Verifikasi Waktu Pemberian')->schema([

                                    Forms\Components\Toggle::make('waktu_is_pagi')
                                        ->label('Pagi (06:00 - 08:00)')
                                        ->live()
                                        ->afterStateUpdated(
                                            fn(Forms\Set $set, $state) =>
                                            $state ? $set('jam', Carbon::now('Asia/Jakarta')->format('H:i:s')) : null
                                        )
                                        ->disabled(
                                            fn(): bool =>
                                            !(now('Asia/Jakarta')->hour >= 6 && now('Asia/Jakarta')->hour < 8)
                                        ),

                                    Forms\Components\Toggle::make('waktu_is_siang')
                                        ->label('Siang (13:00 - 14:00)')
                                        ->live()
                                        ->afterStateUpdated(
                                            fn(Forms\Set $set, $state) =>
                                            $state ? $set('jam', Carbon::now('Asia/Jakarta')->format('H:i:s')) : null
                                        )
                                        ->disabled(
                                            fn(): bool =>
                                            !(now('Asia/Jakarta')->hour >= 11 && now('Asia/Jakarta')->hour < 13)
                                        ),

                                    Forms\Components\Toggle::make('waktu_is_sore')
                                        ->label('Sore (17:00 - 18:00)')
                                        ->live()
                                        ->afterStateUpdated(
                                            fn(Forms\Set $set, $state) =>
                                            $state ? $set('jam', Carbon::now('Asia/Jakarta')->format('H:i:s')) : null
                                        )
                                        ->disabled(
                                            fn(): bool =>
                                            !(now('Asia/Jakarta')->hour >= 16 && now('Asia/Jakarta')->hour < 18)
                                        ),

                                    Forms\Components\Toggle::make('waktu_is_malam')
                                        ->label('Malam (21:00 - 22:00)')
                                        ->live()
                                        ->afterStateUpdated(
                                            fn(Forms\Set $set, $state) =>
                                            $state ? $set('jam', Carbon::now('Asia/Jakarta')->format('H:i:s')) : null
                                        )
                                        ->disabled(
                                            fn(): bool =>
                                            !(now('Asia/Jakarta')->hour >= 19 && now('Asia/Jakarta')->hour < 22)
                                        ),

                                ])->columns(2),

                                Forms\Components\TextInput::make('jam')
                                    ->label('Jam Saat Ini')
                                    ->disabled()
                                    ->dehydrated()
                                    ->default(null),
                                Forms\Components\Textarea::make('waktu_keterangan')->label('Keterangan')->rows(2),
                            ]),
                        Forms\Components\Section::make('8. Benar Pengkajian')
                            ->icon('heroicon-o-magnifying-glass')->collapsible()->collapsed()->schema([
                                Forms\Components\Toggle::make('pengkajian_check_all')
                                    ->label('Pilih Semua')
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set) {
                                        $set('pengkajian_is_suhu', $state);
                                        $set('pengkajian_is_tensi', $state);
                                    }),
                                Forms\Components\Fieldset::make('Verifikasi Pengkajian Awal')->schema([
                                    Forms\Components\Toggle::make('pengkajian_is_suhu')
                                        ->label('Apakah Suhu sudah sesuai?')
                                        ->hint('Centang jika suhu tubuh pasien sudah didokumentasikan dengan benar.')
                                        ->required(),
                                    Forms\Components\Toggle::make('pengkajian_is_tensi')
                                        ->label('Apakah Tensi (Tekanan Darah) sudah sesuai?')
                                        ->hint('Centang jika tekanan darah pasien sudah didokumentasikan dengan benar.')
                                        ->required(),
                                ])->columns(1),
                                Forms\Components\Textarea::make('pengkajian_keterangan')->label('Keterangan')->rows(2),
                            ]),
                        Forms\Components\Section::make('11. Benar Hak Klien')
                            ->icon('heroicon-o-shield-check')->collapsible()->collapsed()->schema([
                                Forms\Components\Toggle::make('hak_check_all')
                                    ->label('Pilih Semua')
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set) {
                                        $set('hak_is_ic', $state);
                                    }),
                                Forms\Components\Fieldset::make('Verifikasi Hak Klien')->schema([
                                    Forms\Components\Toggle::make('hak_is_ic')
                                        ->label('Apakah Informed Consent sudah diberikan?')
                                        ->hint('Centang jika persetujuan (informed consent) dari client sudah didapatkan.')
                                        ->required(),
                                ])->columns(1),
                                Forms\Components\Textarea::make('hak_keterangan')->label('Keterangan')->rows(2),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $formData = $this->form->getState();

        DB::transaction(function () use ($formData) {
            Pemeriksaan::create(array_merge($formData, [
                'status' => 'Pra Tindakan',
                'user_id' => Auth::id(),
                'tanggal' => now('Asia/Jakarta')->toDateString(),
                'jam' => $formData['jam'] ?? now('Asia/Jakarta')->toTimeString(),
            ]));
        });

        Notification::make()
            ->title('Checklist Pra Tindakan Berhasil Disimpan')
            ->success()
            ->send();

        $this->redirect(PemeriksaanResource::getUrl('index'));
    }
}
