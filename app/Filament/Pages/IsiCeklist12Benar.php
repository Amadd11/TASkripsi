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
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;

class IsiCeklist12Benar extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static string $view = 'filament.pages.isi-ceklist12-benar';
    protected static ?string $title = 'Isi Ceklist 12 Benar';
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
                            Forms\Components\Fieldset::make('Verifikasi Waktu Pemberian')->schema([
                                Forms\Components\Toggle::make('waktu_is_pagi')->label('Pagi (06:00 - 10:00)')
                                    ->hint('Centang jika pemberian sesuai waktu pagi.')->required(),
                                Forms\Components\Toggle::make('waktu_is_siang')->label('Siang (11:00 - 14:00)')
                                    ->hint('Centang jika pemberian sesuai waktu siang.')->required(),
                                Forms\Components\Toggle::make('waktu_is_sore')->label('Sore (15:00 - 18:00)')
                                    ->hint('Centang jika pemberian sesuai waktu sore.')->required(),
                                Forms\Components\Toggle::make('waktu_is_malam')->label('Malam (19:00 - 22:00)')
                                    ->hint('Centang jika pemberian sesuai waktu malam.')->required(),
                            ])->columns(1),
                            Forms\Components\Textarea::make('waktu_keterangan')->label('Keterangan')->rows(2),
                        ]),

                        Forms\Components\Tabs\Tab::make('6. Benar Dokumentasi')->schema([
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
                            Forms\Components\Fieldset::make('Verifikasi Reaksi Terhadap Makanan')->schema([
                                Forms\Components\Toggle::make('reaksi_makanan_is_efek_makanan')
                                    ->label('Apakah ada Efek Reaksi Makanan?')
                                    ->hint('Centang jika ada efek reaksi makanan yang didokumentasikan.')
                                    ->required(),
                            ])->columns(1),
                            Forms\Components\Textarea::make('reaksi_makanan_keterangan')->label('Keterangan')->rows(2),
                        ]),

                        Forms\Components\Tabs\Tab::make('11. Benar Hak Klien')->schema([
                            Forms\Components\Fieldset::make('Verifikasi Hak Klien')->schema([
                                Forms\Components\Toggle::make('hak_is_ic')
                                    ->label('Apakah Informed Consent sudah diberikan?')
                                    ->hint('Centang jika persetujuan (informed consent) dari client sudah didapatkan.')
                                    ->required(),
                            ])->columns(1),
                            Forms\Components\Textarea::make('hak_keterangan')->label('Keterangan')->rows(2),
                        ]),

                        Forms\Components\Tabs\Tab::make('12. Benar Pendidikan')->schema([
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
