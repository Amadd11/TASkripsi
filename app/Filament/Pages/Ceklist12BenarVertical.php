<?php

namespace App\Filament\Pages;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Models\BenarCara;
use App\Models\BenarObat;
use App\Models\BenarDosis;
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

class Ceklist12BenarVertical extends Page implements HasForms
{
    use HasPageShield;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static string $view = 'filament.pages.ceklist12-benar-vertical';
    protected static ?string $title = 'Form 12 Benar ';
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

                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Pra Tindakan')
                        ->description('Verifikasi sebelum pemberian obat.')
                        ->schema([
                            Forms\Components\Section::make('1. Benar Pasien')
                                ->icon('heroicon-o-user-circle')->collapsible()->schema([
                                    Forms\Components\Fieldset::make('Verifikasi Identitas Pasien')->schema([
                                        Forms\Components\Radio::make('pasien_is_nama')->label('Apakah Nama Pasien Benar?')->options([true => 'Ya', false => 'Tidak'])->inline(),
                                        Forms\Components\Radio::make('pasien_is_tgl_lahir')->label('Apakah Tanggal Lahir Benar?')->options([true => 'Ya', false => 'Tidak'])->inline(),
                                    ])->columns(1),
                                    Forms\Components\Textarea::make('pasien_keterangan')->label('Keterangan')->rows(2),
                                ]),
                            Forms\Components\Section::make('2. Benar Obat')
                                ->icon('heroicon-o-beaker')->collapsible()->collapsed()->schema([
                                    Forms\Components\Fieldset::make('Verifikasi Obat')->schema([
                                        Forms\Components\Radio::make('obat_is_nama_obat')->label('Apakah Nama Obat Benar?')->options([true => 'Ya', false => 'Tidak'])->inline(),
                                        Forms\Components\Radio::make('obat_is_label')->label('Apakah Label Obat Benar?')->options([true => 'Ya', false => 'Tidak'])->inline(),
                                        Forms\Components\Radio::make('obat_is_resep')->label('Apakah Resep Benar?')->options([true => 'Ya', false => 'Tidak'])->inline(),
                                    ])->columns(1),
                                    Forms\Components\Textarea::make('obat_keterangan')->label('Keterangan')->rows(2),
                                ]),
                            Forms\Components\Section::make('3. Benar Dosis')
                                ->icon('heroicon-o-scale')->collapsible()->collapsed()->schema([
                                    Forms\Components\Fieldset::make('Verifikasi Dosis')->schema([
                                        Forms\Components\Radio::make('dosis_is_jumlah')->label('Apakah Jumlah/Dosis sudah sesuai?')->options([true => 'Ya', false => 'Tidak'])->inline(),
                                        Forms\Components\Radio::make('dosis_is_potensi')->label('Apakah Potensi/Kekuatan sudah sesuai?')->options([true => 'Ya', false => 'Tidak'])->inline(),
                                    ])->columns(1),
                                    Forms\Components\Textarea::make('dosis_keterangan')->label('Keterangan')->rows(2),
                                ]),
                            Forms\Components\Section::make('4. Benar Cara')
                                ->icon('heroicon-o-paper-airplane')->collapsible()->collapsed()->schema([
                                    Forms\Components\Fieldset::make('Verifikasi Cara Pemberian')->schema([
                                        Forms\Components\Radio::make('is_oral')->label('Oral')->options([true => 'Ya', false => 'Tidak'])->inline(),
                                        Forms\Components\Radio::make('is_iv')->label('Intravena (IV)')->options([true => 'Ya', false => 'Tidak'])->inline(),
                                        Forms\Components\Radio::make('is_im')->label('Intramuskular (IM)')->options([true => 'Ya', false => 'Tidak'])->inline(),
                                        Forms\Components\Radio::make('is_intratekal')->label('Intratekal')->options([true => 'Ya', false => 'Tidak'])->inline(),
                                        Forms\Components\Radio::make('is_subkutan')->label('Subkutan')->options([true => 'Ya', false => 'Tidak'])->inline(),
                                        Forms\Components\Radio::make('is_sublingual')->label('Sublingual')->options([true => 'Ya', false => 'Tidak'])->inline(),
                                        Forms\Components\Radio::make('is_rektal')->label('Rektal')->options([true => 'Ya', false => 'Tidak'])->inline(),
                                        Forms\Components\Radio::make('is_vaginal')->label('Vaginal')->options([true => 'Ya', false => 'Tidak'])->inline(),
                                        Forms\Components\Radio::make('is_okular')->label('Okular (Mata)')->options([true => 'Ya', false => 'Tidak'])->inline(),
                                        Forms\Components\Radio::make('is_otik')->label('Otik (Telinga)')->options([true => 'Ya', false => 'Tidak'])->inline(),
                                        Forms\Components\Radio::make('is_nasal')->label('Nasal (Hidung)')->options([true => 'Ya', false => 'Tidak'])->inline(),
                                        Forms\Components\Radio::make('is_nebulisasi')->label('Nebulisasi')->options([true => 'Ya', false => 'Tidak'])->inline(),
                                        Forms\Components\Radio::make('is_topikal')->label('Topikal (Kulit)')->options([true => 'Ya', false => 'Tidak'])->inline(),
                                    ])->columns(2),
                                    Forms\Components\Textarea::make('cara_keterangan')->label('Keterangan')->rows(2),
                                ]),
                            Forms\Components\Section::make('5. Benar Waktu')
                                ->icon('heroicon-o-clock')->collapsible()->collapsed()->schema([
                                    Forms\Components\Fieldset::make('Verifikasi Waktu Pemberian')->schema([
                                        Forms\Components\Radio::make('waktu_is_pagi')->label('Pagi (06:00 - 08:00)')->options([true => 'Ya', false => 'Tidak'])->inline()->live()->afterStateUpdated(fn(Forms\Set $set, $state) => $state ? $set('jam', now('Asia/Jakarta')->format('H:i:s')) : $set('jam', null))->disabled(fn(): bool => !(now('Asia/Jakarta')->hour >= 6 && now('Asia/Jakarta')->hour < 8)),
                                        Forms\Components\Radio::make('waktu_is_siang')->label('Siang (13:00 - 14:00)')->options([true => 'Ya', false => 'Tidak'])->inline()->live()->afterStateUpdated(fn(Forms\Set $set, $state) => $state ? $set('jam', now('Asia/Jakarta')->format('H:i:s')) : $set('jam', null))->disabled(fn(): bool => !(now('Asia/Jakarta')->hour >= 11 && now('Asia/Jakarta')->hour < 13)),
                                        Forms\Components\Radio::make('waktu_is_sore')->label('Sore (17:00 - 18:00)')->options([true => 'Ya', false => 'Tidak'])->inline()->live()->afterStateUpdated(fn(Forms\Set $set, $state) => $state ? $set('jam', now('Asia/Jakarta')->format('H:i:s')) : $set('jam', null))->disabled(fn(): bool => !(now('Asia/Jakarta')->hour >= 16 && now('Asia/Jakarta')->hour < 18)),
                                        Forms\Components\Radio::make('waktu_is_malam')->label('Malam (21:00 - 22:00)')->options([true => 'Ya', false => 'Tidak'])->inline()->live()->afterStateUpdated(fn(Forms\Set $set, $state) => $state ? $set('jam', now('Asia/Jakarta')->format('H:i:s')) : $set('jam', null))->disabled(fn(): bool => !(now('Asia/Jakarta')->hour >= 19 && now('Asia/Jakarta')->hour < 22)),
                                    ])->columns(2),
                                    Forms\Components\TextInput::make('jam')->label('Jam Saat Ini')->disabled()->dehydrated(),
                                    Forms\Components\Textarea::make('waktu_keterangan')->label('Keterangan')->rows(2),
                                ]),
                            Forms\Components\Section::make('6. Benar Pengkajian')
                                ->icon('heroicon-o-magnifying-glass')->collapsible()->collapsed()->schema([
                                    Forms\Components\Fieldset::make('Verifikasi Pengkajian Awal')->schema([
                                        Forms\Components\Radio::make('pengkajian_is_suhu')->label('Suhu sudah sesuai?')->options([true => 'Ya', false => 'Tidak'])->inline(),
                                        Forms\Components\Radio::make('pengkajian_is_tensi')->label('Tekanan darah sudah sesuai?')->options([true => 'Ya', false => 'Tidak'])->inline(),
                                        Forms\Components\Radio::make('pengkajian_is_riwayat_alergi')->label('Apakah ada Riwayat Alergi ?')->options([true => 'Ya', false => 'Tidak'])->inline(),
                                    ])->columns(1),
                                    Forms\Components\Textarea::make('pengkajian_keterangan')->label('Keterangan')->rows(2),
                                ]),
                            Forms\Components\Section::make('7. Benar Hak Klien')
                                ->icon('heroicon-o-shield-check')->collapsible()->collapsed()->schema([
                                    Forms\Components\Toggle::make('hak_check_all')->label('Pilih Semua')->live()->afterStateUpdated(fn($state, Forms\Set $set) => $set('hak_is_ic', $state)),
                                    Forms\Components\Radio::make('hak_is_ic')->label('Apakah Informed Consent sudah diberikan?')->options([true => 'Ya', false => 'Tidak'])->inline(),
                                    Forms\Components\Textarea::make('hak_keterangan')->label('Keterangan')->rows(2),
                                ]),
                        ]),
                    Forms\Components\Wizard\Step::make('Pasca Tindakan')
                        ->description('Verifikasi setelah pemberian obat.')
                        ->schema([
                            Forms\Components\Section::make('8. Benar Dokumentasi')
                                ->icon('heroicon-o-document-text')->collapsible()->schema([
                                    Forms\Components\Fieldset::make('Verifikasi Kelengkapan Dokumentasi')->schema([
                                        Forms\Components\Radio::make('dok_is_pasien')->label('Dokumentasi Pasien Benar?')->options([true => 'Ya', false => 'Tidak'])->inline(),
                                        Forms\Components\Radio::make('dok_is_dosis')->label('Dokumentasi Dosis Benar?')->options([true => 'Ya', false => 'Tidak'])->inline(),
                                        Forms\Components\Radio::make('dok_is_obat')->label('Dokumentasi Obat Benar?')->options([true => 'Ya', false => 'Tidak'])->inline(),
                                        Forms\Components\Radio::make('dok_is_waktu')->label('Dokumentasi Waktu Benar?')->options([true => 'Ya', false => 'Tidak'])->inline(),
                                        Forms\Components\Radio::make('dok_is_rute')->label('Dokumentasi Rute Benar?')->options([true => 'Ya', false => 'Tidak'])->inline(),
                                    ])->columns(1),
                                    Forms\Components\Textarea::make('dok_keterangan')->label('Keterangan')->rows(2),
                                ]),
                            Forms\Components\Section::make('9. Benar Evaluasi')
                                ->icon('heroicon-o-check-circle')->collapsible()->collapsed()->schema([
                                    Forms\Components\Fieldset::make('Verifikasi Evaluasi Pasien')->schema([
                                        Forms\Components\Radio::make('evaluasi_is_efek_samping')->label('Ada Efek Samping?')->options([true => 'Ya', false => 'Tidak'])->inline(),
                                        Forms\Components\Radio::make('evaluasi_is_alergi')->label('Ada Reaksi Alergi?')->options([true => 'Ya', false => 'Tidak'])->inline(),
                                        Forms\Components\Radio::make('evaluasi_is_efek_terapi')->label('Efek Terapi tercapai?')->options([true => 'Ya', false => 'Tidak'])->inline(),
                                    ])->columns(1),
                                    Forms\Components\Textarea::make('evaluasi_keterangan')->label('Keterangan')->rows(2),
                                ]),
                            Forms\Components\Section::make('10. Benar Reaksi Obat')
                                ->icon('heroicon-o-exclamation-triangle')->collapsible()->collapsed()->schema([
                                    Forms\Components\Fieldset::make('Verifikasi Reaksi Terhadap Obat Lain')->schema([
                                        Forms\Components\Radio::make('reaksi_obat_is_efek_samping')->label('Ada Efek Samping?')->options([true => 'Ya', false => 'Tidak'])->inline(),
                                        Forms\Components\Radio::make('reaksi_obat_is_alergi')->label('Ada Reaksi Alergi?')->options([true => 'Ya', false => 'Tidak'])->inline(),
                                        Forms\Components\Radio::make('reaksi_obat_is_efek_terapi')->label('Efek Terapi tercapai?')->options([true => 'Ya', false => 'Tidak'])->inline(),
                                    ])->columns(1),
                                    Forms\Components\Textarea::make('reaksi_obat_keterangan')->label('Keterangan')->rows(2),
                                ]),
                            Forms\Components\Section::make('11. Benar Reaksi Makanan')
                                ->icon('heroicon-o-cake')->collapsible()->collapsed()->schema([
                                    Forms\Components\Toggle::make('reaksi_makanan_check_all')->label('Pilih Semua')->live()->afterStateUpdated(fn($state, Forms\Set $set) => $set('reaksi_makanan_is_efek_makanan', $state)),
                                    Forms\Components\Radio::make('reaksi_makanan_is_efek_makanan')->label('Apakah ada Efek Reaksi Makanan?')->options([true => 'Ya', false => 'Tidak'])->inline(),
                                    Forms\Components\Textarea::make('reaksi_makanan_keterangan')->label('Keterangan')->rows(2),
                                ]),
                            Forms\Components\Section::make('12. Benar Pendidikan')
                                ->icon('heroicon-o-academic-cap')->collapsible()->collapsed()->schema([
                                    Forms\Components\Toggle::make('pendidikan_check_all')->label('Pilih Semua')->live()->afterStateUpdated(fn($state, Forms\Set $set) => $set('pendidikan_is_edukasi', $state)),
                                    Forms\Components\Radio::make('pendidikan_is_edukasi')->label('Apakah Edukasi sudah diberikan?')->options([true => 'Ya', false => 'Tidak'])->inline(),
                                    Forms\Components\Textarea::make('pendidikan_keterangan')->label('Keterangan')->rows(2),
                                ]),
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
                'jam' => $formData['jam'] ?? now('Asia/Jakarta')->toTimeString(),
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

            // Update BenarCara to save all 13 fields
            BenarCara::create(array_merge($commonData, [
                'is_oral' => $formData['is_oral'] ?? false,
                'is_iv' => $formData['is_iv'] ?? false,
                'is_im' => $formData['is_im'] ?? false,
                'is_intratekal' => $formData['is_intratekal'] ?? false,
                'is_subkutan' => $formData['is_subkutan'] ?? false,
                'is_sublingual' => $formData['is_sublingual'] ?? false,
                'is_rektal' => $formData['is_rektal'] ?? false,
                'is_vaginal' => $formData['is_vaginal'] ?? false,
                'is_okular' => $formData['is_okular'] ?? false,
                'is_otik' => $formData['is_otik'] ?? false,
                'is_nasal' => $formData['is_nasal'] ?? false,
                'is_nebulisasi' => $formData['is_nebulisasi'] ?? false,
                'is_topikal' => $formData['is_topikal'] ?? false,
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
                'is_riwayat_alergi' => $formData['pengkajian_is_riwayat_alergi'] ?? false,
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
