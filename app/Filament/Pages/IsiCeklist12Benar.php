<?php

namespace App\Filament\Pages;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Models\BenarCara;
use App\Models\BenarObat;
use App\Models\BenarDosis;

// Import semua model yang relevan
use App\Models\BenarWaktu;
use App\Models\BenarPasien;
use App\Models\MasterPasien;
use App\Models\BenarEvaluasi;
use App\Models\BenarHakClient;
use App\Models\BenarPendidikan;
use App\Models\BenarPengkajian;
use App\Models\BenarReaksiObat;
use App\Models\BenarDokumentasi;
use App\Models\BenarReaksiMakanan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class IsiCeklist12Benar extends Page implements HasForms
{
    use HasPageShield;

    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static string $view = 'filament.pages.isi-ceklist12-benar';
    protected static ?string $title = 'Ceklist 12 Benar';
    protected static ?string $navigationGroup = 'Pemeriksaan';

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
                                    // Kosongkan field jika tidak ada pasien yang dipilih
                                    $set('nama_pas', null);
                                    $set('tgl_lahir', null);
                                    $set('no_reg', null);
                                    return;
                                }
                                $pasien = MasterPasien::where('no_cm', $state)->first();
                                if ($pasien) {
                                    $set('nama_pas', $pasien->nama_pas);
                                    $set('tgl_lahir', Carbon::parse($pasien->tgl_lahir)->translatedFormat('d F Y'));
                                    // DIUBAH: Mengisi no_reg secara otomatis
                                    $set('no_reg', $pasien->no_reg);
                                }
                            }),

                        // DIUBAH: Menambahkan field untuk Nomor Registrasi
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

                Forms\Components\Tabs::make('Checklist 12 Benar')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('1. Benar Pasien')->schema([
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

                        Forms\Components\Tabs\Tab::make('2. Benar Obat')->schema([
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

                        Forms\Components\Tabs\Tab::make('3. Benar Dosis')->schema([
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

                        Forms\Components\Tabs\Tab::make('4. Benar Cara')->schema([
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

                        Forms\Components\Tabs\Tab::make('5. Benar Waktu')->schema([
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

                        Forms\Components\Tabs\Tab::make('6. Benar Dokumentasi')->schema([
                            Forms\Components\Toggle::make('dok_check_all')
                                ->label('Pilih Semua')
                                ->live()
                                ->afterStateUpdated(function ($state, $set) {
                                    $set('dok_is_pasien', $state);
                                    $set('dok_is_dosis', $state);
                                    $set('dok_is_obat', $state);
                                    $set('dok_is_waktu', $state);
                                    $set('dok_is_rute', $state);
                                }),
                            Forms\Components\Fieldset::make('Verifikasi Kelengkapan Dokumentasi')->schema([
                                Forms\Components\Toggle::make('dok_is_pasien')
                                    ->label('Apakah Dokumentasi Pasien Benar?')
                                    ->hint('Centang jika informasi pasien didokumentasikan dengan benar.')
                                    ->required(),
                                Forms\Components\Toggle::make('dok_is_dosis')
                                    ->label('Apakah Dokumentasi Dosis Benar?')
                                    ->hint('Centang jika dosis didokumentasikan dengan benar.')
                                    ->required(),
                                Forms\Components\Toggle::make('dok_is_obat')
                                    ->label('Apakah Dokumentasi Obat Benar?')
                                    ->hint('Centang jika nama obat didokumentasikan dengan benar.')
                                    ->required(),
                                Forms\Components\Toggle::make('dok_is_waktu')
                                    ->label('Apakah Dokumentasi Waktu Pemberian Benar?')
                                    ->hint('Centang jika waktu pemberian didokumentasikan dengan benar.')
                                    ->required(),
                                Forms\Components\Toggle::make('dok_is_rute')
                                    ->label('Apakah Dokumentasi Rute Pemberian Benar?')
                                    ->hint('Centang jika rute pemberian didokumentasikan dengan benar.')
                                    ->required(),
                            ])->columns(1),
                            Forms\Components\Textarea::make('dok_keterangan')->label('Keterangan')->rows(2),
                        ]),

                        Forms\Components\Tabs\Tab::make('7. Benar Evaluasi')->schema([
                            Forms\Components\Toggle::make('evaluasi_check_all')
                                ->label('Pilih Semua')
                                ->live()
                                ->afterStateUpdated(function ($state, $set) {
                                    $set('evaluasi_is_efek_samping', $state);
                                    $set('evaluasi_is_alergi', $state);
                                    $set('evaluasi_is_efek_terapi', $state);
                                }),
                            Forms\Components\Fieldset::make('Verifikasi Evaluasi Pasien')->schema([
                                Forms\Components\Toggle::make('evaluasi_is_efek_samping')
                                    ->label('Apakah ada Efek Samping?')
                                    ->hint('Centang jika ada efek samping yang didokumentasikan.')
                                    ->required(),
                                Forms\Components\Toggle::make('evaluasi_is_alergi')
                                    ->label('Apakah ada Reaksi Alergi?')
                                    ->hint('Centang jika ada reaksi alergi yang didokumentasikan.')
                                    ->required(),
                                Forms\Components\Toggle::make('evaluasi_is_efek_terapi')
                                    ->label('Apakah Efek Terapi tercapai?')
                                    ->hint('Centang jika efek terapi yang diharapkan tercapai.')
                                    ->required(),
                            ])->columns(1),
                            Forms\Components\Textarea::make('evaluasi_keterangan')->label('Keterangan')->rows(2),
                        ]),

                        Forms\Components\Tabs\Tab::make('8. Benar Pengkajian')->schema([
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

                        Forms\Components\Tabs\Tab::make('9. Benar Reaksi Obat')->schema([
                            Forms\Components\Toggle::make('reaksi_obat_check_all')
                                ->label('Pilih Semua')
                                ->live()
                                ->afterStateUpdated(function ($state, $set) {
                                    $set('reaksi_obat_is_efek_samping', $state);
                                    $set('reaksi_obat_is_alergi', $state);
                                    $set('reaksi_obat_is_efek_terapi', $state);
                                }),
                            Forms\Components\Fieldset::make('Verifikasi Reaksi Terhadap Obat Lain')->schema([
                                Forms\Components\Toggle::make('reaksi_obat_is_efek_samping')
                                    ->label('Apakah ada Efek Samping?')
                                    ->hint('Centang jika ada efek samping obat yang didokumentasikan.')
                                    ->required(),
                                Forms\Components\Toggle::make('reaksi_obat_is_alergi')
                                    ->label('Apakah ada Reaksi Alergi?')
                                    ->hint('Centang jika ada reaksi alergi obat yang didokumentasikan.')
                                    ->required(),
                                Forms\Components\Toggle::make('reaksi_obat_is_efek_terapi')
                                    ->label('Apakah Efek Terapi tercapai?')
                                    ->hint('Centang jika efek terapi yang diharapkan dari obat tercapai.')
                                    ->required(),
                            ])->columns(1),
                            Forms\Components\Textarea::make('reaksi_obat_keterangan')->label('Keterangan')->rows(2),
                        ]),

                        Forms\Components\Tabs\Tab::make('10. Benar Reaksi Makanan')->schema([
                            Forms\Components\Toggle::make('reaksi_makanan_check_all')
                                ->label('Pilih Semua')
                                ->live()
                                ->afterStateUpdated(function ($state, $set) {
                                    $set('reaksi_makanan_is_efek_makanan', $state);
                                }),
                            Forms\Components\Fieldset::make('Verifikasi Reaksi Terhadap Makanan')->schema([
                                Forms\Components\Toggle::make('reaksi_makanan_is_efek_makanan')
                                    ->label('Apakah ada Efek Reaksi Makanan?')
                                    ->hint('Centang jika ada efek reaksi makanan yang didokumentasikan.')
                                    ->required(),
                            ])->columns(1),
                            Forms\Components\Textarea::make('reaksi_makanan_keterangan')->label('Keterangan')->rows(2),
                        ]),

                        Forms\Components\Tabs\Tab::make('11. Benar Hak Klien')->schema([
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

                        Forms\Components\Tabs\Tab::make('12. Benar Pendidikan')->schema([
                            Forms\Components\Toggle::make('pendidikan_check_all')
                                ->label('Pilih Semua')
                                ->live()
                                ->afterStateUpdated(function ($state, $set) {
                                    $set('pendidikan_is_edukasi', $state);
                                }),
                            Forms\Components\Fieldset::make('Verifikasi Pendidikan Kesehatan')->schema([
                                Forms\Components\Toggle::make('pendidikan_is_edukasi')
                                    ->label('Apakah Edukasi sudah diberikan?')
                                    ->hint('Centang jika edukasi kepada pasien sudah diberikan dan didokumentasikan.')
                                    ->required(),
                            ])->columns(1),
                            Forms\Components\Textarea::make('pendidikan_keterangan')->label('Keterangan')->rows(2),
                        ]),
                    ])->columnSpanFull(),

            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $formData = $this->form->getState();

        DB::transaction(function () use ($formData) {
            $commonData = [
                'no_cm' => $formData['no_cm'],
                'no_reg' => $formData['no_reg'] ?? null,
                'user_id' => Auth::id(),
                'tanggal' => now('Asia/Jakarta')->toDateString(),
                'jam' => now('Asia/Jakarta')->toTimeString(),
            ];

            BenarPasien::create(array_merge($commonData, [
                'is_nama' => $formData['pasien_is_nama'] ?? false,
                'is_tgl_lahir' => $formData['pasien_is_tgl_lahir'] ?? false,
                'keterangan' => $formData['pasien_keterangan'] ?? null,
            ]));

            BenarObat::create(array_merge($commonData, [
                'is_nama_obat' => $formData['obat_is_nama_obat'] ?? false,
                'is_label' => $formData['obat_is_label'] ?? false,
                'is_resep' => $formData['obat_is_resep'] ?? false,
                'keterangan' => $formData['obat_keterangan'] ?? null,
            ]));

            BenarDosis::create(array_merge($commonData, [
                'is_jumlah' => $formData['dosis_is_jumlah'] ?? false,
                'is_potensi' => $formData['dosis_is_potensi'] ?? false,
                'keterangan' => $formData['dosis_keterangan'] ?? null,
            ]));

            BenarWaktu::create(array_merge($commonData, [
                'is_pagi' => $formData['waktu_is_pagi'] ?? false,
                'is_siang' => $formData['waktu_is_siang'] ?? false,
                'is_sore' => $formData['waktu_is_sore'] ?? false,
                'is_malam' => $formData['waktu_is_malam'] ?? false,
                'keterangan' => $formData['waktu_keterangan'] ?? null,
            ]));

            BenarCara::create(array_merge($commonData, [
                'is_oral' => $formData['cara_is_oral'] ?? false,
                'is_iv' => $formData['cara_is_iv'] ?? false,
                'is_im' => $formData['cara_is_im'] ?? false,
                'keterangan' => $formData['cara_keterangan'] ?? null,
            ]));

            BenarDokumentasi::create(array_merge($commonData, [
                'is_pasien' => $formData['dok_is_pasien'] ?? false,
                'is_dosis' => $formData['dok_is_dosis'] ?? false,
                'is_obat' => $formData['dok_is_obat'] ?? false,
                'is_waktu' => $formData['dok_is_waktu'] ?? false,
                'is_rute' => $formData['dok_is_rute'] ?? false,
                'keterangan' => $formData['dok_keterangan'] ?? null,
            ]));

            BenarEvaluasi::create(array_merge($commonData, [
                'is_efek_samping' => $formData['evaluasi_is_efek_samping'] ?? false,
                'is_alergi' => $formData['evaluasi_is_alergi'] ?? false,
                'is_efek_terapi' => $formData['evaluasi_is_efek_terapi'] ?? false,
                'keterangan' => $formData['evaluasi_keterangan'] ?? null,
            ]));

            BenarPengkajian::create(array_merge($commonData, [
                'is_suhu' => $formData['pengkajian_is_suhu'] ?? false,
                'is_tensi' => $formData['pengkajian_is_tensi'] ?? false,
                'keterangan' => $formData['pengkajian_keterangan'] ?? null,
            ]));

            BenarReaksiObat::create(array_merge($commonData, [
                'is_efek_samping' => $formData['reaksi_obat_is_efek_samping'] ?? false,
                'is_alergi' => $formData['reaksi_obat_is_alergi'] ?? false,
                'is_efek_terapi' => $formData['reaksi_obat_is_efek_terapi'] ?? false,
                'keterangan' => $formData['reaksi_obat_keterangan'] ?? null,
            ]));

            BenarReaksiMakanan::create(array_merge($commonData, [
                'is_efek_makanan' => $formData['reaksi_makanan_is_efek_makanan'] ?? false,
                'keterangan' => $formData['reaksi_makanan_keterangan'] ?? null,
            ]));

            BenarHakClient::create(array_merge($commonData, [
                'is_ic' => $formData['hak_is_ic'] ?? false,
                'keterangan' => $formData['hak_keterangan'] ?? null,
            ]));

            BenarPendidikan::create(array_merge($commonData, [
                'is_edukasi' => $formData['pendidikan_is_edukasi'] ?? false,
                'keterangan' => $formData['pendidikan_keterangan'] ?? null,
            ]));
        });

        Notification::make()
            ->title('Checklist Berhasil Disimpan')
            ->success()
            ->send();

        $this->form->fill();
    }
}
