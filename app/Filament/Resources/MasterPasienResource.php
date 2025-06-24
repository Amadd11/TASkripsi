<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MasterPasienResource\Pages;
use App\Filament\Resources\MasterPasienResource\RelationManagers;
use App\Models\MasterPasien;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TimePicker; // Import TimePicker

class MasterPasienResource extends Resource
{
    protected static ?string $model = MasterPasien::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identitas Pasien')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('no_cm')
                            ->label('Nomor CM')
                            ->unique()
                            ->required()
                            ->maxLength(8),
                        Forms\Components\TextInput::make('nama_pas')
                            ->label('Nama Pasien')
                            ->required()
                            ->maxLength(40),
                        Forms\Components\DatePicker::make('tgl_lahir')
                            ->label('Tanggal Lahir')
                            ->native(false)
                            ->nullable(),
                        Forms\Components\Select::make('sex')
                            ->label('Jenis Kelamin')
                            ->options([
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            ])
                            ->nullable(),
                        Forms\Components\TextInput::make('no_reg')
                            ->label('Nomor Registrasi')
                            ->maxLength(8)
                            ->placeholder('REG00001')
                            ->nullable(),
                        Forms\Components\DateTimePicker::make('tgl_kunj')
                            ->label('Tanggal Kunjungan')
                            ->native(false)
                            ->nullable(),
                    ]),

                // 2. Alamat & Kontak Pasien
                Forms\Components\Section::make('Alamat & Kontak Pasien')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('alamat')
                            ->label('Alamat Lengkap')
                            ->maxLength(200)
                            ->columnSpanFull()
                            ->nullable(),
                        Forms\Components\TextInput::make('desa')
                            ->label('Desa')
                            ->required()
                            ->maxLength(15)
                            ->default('-'),
                        Forms\Components\TextInput::make('kec')
                            ->label('Kecamatan')
                            ->maxLength(10)
                            ->nullable(),
                        Forms\Components\TextInput::make('kab')
                            ->label('Kabupaten')
                            ->maxLength(10)
                            ->nullable(),
                        Forms\Components\TextInput::make('telp')
                            ->label('Telepon Pasien')
                            ->tel()
                            ->maxLength(20)
                            ->nullable(),
                        Forms\Components\TextInput::make('nik')
                            ->label('NIK')
                            ->maxLength(20)
                            ->nullable(),
                    ]),

                // 3. Informasi Orang Tua / Wali
                Forms\Components\Section::make('Orang Tua / Wali')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('nama_ortu')
                            ->label('Nama Orang Tua')
                            ->maxLength(30)
                            ->nullable(),
                        Forms\Components\TextInput::make('alm_ortu')
                            ->label('Alamat Orang Tua')
                            ->maxLength(200)
                            ->nullable(),
                        Forms\Components\TextInput::make('pek_ortu')
                            ->label('Pekerjaan Orang Tua')
                            ->maxLength(30)
                            ->nullable(),
                        Forms\Components\TextInput::make('telp_ortu')
                            ->label('Telepon Orang Tua')
                            ->tel()
                            ->maxLength(20)
                            ->nullable(),
                    ]),

                // 4. Status Sosial & Pendidikan Pasien
                Forms\Components\Section::make('Status Sosial & Pendidikan Pasien')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('hub')
                            ->label('Hubungan')
                            ->maxLength(15)
                            ->nullable(),
                        Forms\Components\TextInput::make('id_menikah')
                            ->label('ID Status Menikah')
                            ->numeric()
                            ->nullable(),
                        Forms\Components\TextInput::make('pend')
                            ->label('Pendidikan Terakhir')
                            ->maxLength(20)
                            ->nullable(),
                        Forms\Components\TextInput::make('agama')
                            ->label('Agama')
                            ->maxLength(10)
                            ->nullable(),
                        Forms\Components\TextInput::make('status')
                            ->label('Status Pasien (Umum)')
                            ->maxLength(30)
                            ->nullable(),
                        Forms\Components\TextInput::make('status_kary')
                            ->label('Status Karyawan')
                            ->maxLength(30)
                            ->nullable(),
                    ]),

                // 5. Pekerjaan Pasien
                Forms\Components\Section::make('Pekerjaan Pasien')
                    ->columns(1)
                    ->schema([
                        Forms\Components\TextInput::make('pek_pasien')
                            ->label('Pekerjaan Pasien')
                            ->maxLength(30)
                            ->nullable(),
                    ]),

                // 6. Kunjungan & Asuransi
                Forms\Components\Section::make('Kunjungan & Asuransi')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('kunjungan')
                            ->label('Jumlah Kunjungan')
                            ->numeric()
                            ->nullable(),
                        Forms\Components\TextInput::make('cara_masuk')
                            ->label('Cara Masuk')
                            ->maxLength(20)
                            ->nullable(),
                        Forms\Components\TextInput::make('polisi')
                            ->label('Catatan Polisi')
                            ->maxLength(5)
                            ->nullable(),
                        Forms\Components\TextInput::make('asuransi')
                            ->label('Asuransi')
                            ->maxLength(40)
                            ->nullable(),
                        Forms\Components\TextInput::make('aktif')
                            ->label('Status Aktif')
                            ->maxLength(6)
                            ->nullable(),
                        Forms\Components\TextInput::make('no_px')
                            ->label('Nomor Pasien (PX)')
                            ->numeric()
                            ->nullable(),
                    ]),

                // 7. Informasi Medis
                Forms\Components\Section::make('Informasi Medis')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('diagnosa')
                            ->label('Diagnosa')
                            ->maxLength(50)
                            ->nullable(),
                        Forms\Components\TextInput::make('kd_dx')
                            ->label('Kode Diagnosa')
                            ->maxLength(6)
                            ->nullable(),
                        Forms\Components\Textarea::make('catatan_bpjs')
                            ->label('Catatan BPJS')
                            ->maxLength(200)
                            ->columnSpanFull()
                            ->nullable(),
                        Forms\Components\TextInput::make('id_alergi')
                            ->label('ID Alergi')
                            ->numeric()
                            ->nullable(),
                        Forms\Components\TextInput::make('fis_asuhan')
                            ->label('Fis Asuhan')
                            ->numeric()
                            ->nullable(),
                        Forms\Components\TextInput::make('id_retensi')
                            ->label('ID Retensi')
                            ->numeric()
                            ->nullable(),
                    ]),

                // 8. Flag & Status Kesehatan Pasien
                Forms\Components\Section::make('Flag & Status Kesehatan Pasien')
                    ->columns(4)
                    ->schema([
                        Forms\Components\Toggle::make('cek_kpsta')
                            ->label('Cek KPSTA'),
                        Forms\Components\Toggle::make('cek_ktp')
                            ->label('Cek KTP'),
                        Forms\Components\Toggle::make('cek_kk')
                            ->label('Cek KK'),
                        Forms\Components\Toggle::make('bank_v_lab')
                            ->label('Verifikasi Bank Lab'),
                        Forms\Components\Toggle::make('bank_v_far')
                            ->label('Verifikasi Bank Farmasi'),
                        Forms\Components\Toggle::make('bank_v_rad')
                            ->label('Verifikasi Bank Radiologi'),
                        Forms\Components\Toggle::make('bank_v_gz')
                            ->label('Verifikasi Bank Gizi'),
                        Forms\Components\Toggle::make('bank_v_fis')
                            ->label('Verifikasi Bank Fisioterapi'),
                        Forms\Components\Toggle::make('flag_mcu')
                            ->label('Flag MCU'),
                        Forms\Components\Toggle::make('flag_penyakit')
                            ->label('Flag Penyakit'),
                        Forms\Components\Toggle::make('flag_pasien')
                            ->label('Flag Pasien'),
                        Forms\Components\Toggle::make('flag_prolanis')
                            ->label('Flag Prolanis'),
                        Forms\Components\Toggle::make('is_hiv')
                            ->label('HIV Positif'),
                        Forms\Components\Toggle::make('is_hbs')
                            ->label('HBS Positif'),
                        Forms\Components\Toggle::make('is_tbc')
                            ->label('TBC Positif'),
                        Forms\Components\Toggle::make('is_pulang')
                            ->label('Sudah Pulang'),
                        Forms\Components\Toggle::make('is_titip')
                            ->label('Is Titip'),
                        Forms\Components\Toggle::make('is_farmasi')
                            ->label('Is Farmasi'),
                        Forms\Components\Toggle::make('is_radiologi')
                            ->label('Is Radiologi'),
                        Forms\Components\Toggle::make('is_laboratorium')
                            ->label('Is Laboratorium'),
                    ]),

                // 9. Domisili & Rujukan
                Forms\Components\Section::make('Domisili & Rujukan')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('alamat_domisili')
                            ->label('Alamat Domisili')
                            ->maxLength(200)
                            ->nullable(),
                        Forms\Components\TextInput::make('lokasi_domisili')
                            ->label('Lokasi Domisili')
                            ->maxLength(250)
                            ->nullable(),
                        Forms\Components\TextInput::make('id_prop_domisili')
                            ->label('ID Propinsi Domisili')
                            ->maxLength(10)
                            ->nullable(),
                        Forms\Components\TextInput::make('id_kab_domisili')
                            ->label('ID Kabupaten Domisili')
                            ->maxLength(10)
                            ->nullable(),
                        Forms\Components\TextInput::make('id_kec_domisili')
                            ->label('ID Kecamatan Domisili')
                            ->maxLength(10)
                            ->nullable(),
                        Forms\Components\TextInput::make('id_desa_domisili')
                            ->label('ID Desa Domisili')
                            ->maxLength(10)
                            ->nullable(),
                        Forms\Components\TextInput::make('asal_daerah')
                            ->label('Asal Daerah')
                            ->maxLength(40)
                            ->nullable(),
                        Forms\Components\TextInput::make('id_propinsi')
                            ->label('ID Propinsi (Rujukan)')
                            ->maxLength(2)
                            ->nullable(),
                        Forms\Components\TextInput::make('no_kpsta')
                            ->label('No. KPSTA')
                            ->maxLength(30)
                            ->nullable(),
                    ]),

                // 10. Informasi Kunjungan Tambahan
                Forms\Components\Section::make('Informasi Kunjungan Tambahan')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('kelas')
                            ->label('Kelas')
                            ->nullable(),
                        Forms\Components\TextInput::make('unit')
                            ->label('Unit')
                            ->nullable(),
                        Forms\Components\TextInput::make('jam')
                            ->label('Jam Kunjungan (Umum)')
                            ->nullable(),
                        Forms\Components\DatePicker::make('tgl_kunj1')
                            ->label('Tanggal Kunjungan Awal')
                            ->native(false)
                            ->nullable(),
                        Forms\Components\DatePicker::make('tgl_kunj2')
                            ->label('Tanggal Kunjungan Akhir')
                            ->native(false)
                            ->nullable(),
                        Forms\Components\TextInput::make('jam1')
                            ->label('Jam Awal')
                            ->nullable(),
                        Forms\Components\TextInput::make('jam2')
                            ->label('Jam Akhir')
                            ->nullable(),
                        Forms\Components\TextInput::make('kls1')
                            ->label('Kelas 1')
                            ->nullable(),
                        Forms\Components\TextInput::make('kls2')
                            ->label('Kelas 2')
                            ->nullable(),
                        Forms\Components\TextInput::make('ruang')
                            ->label('Ruang')
                            ->nullable(),
                    ]),

                // 11. Informasi Staf & Pengirim
                Forms\Components\Section::make('Informasi Staf & Pengirim')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('petugas_tpp')
                            ->label('Petugas TPP')
                            ->nullable(),
                        Forms\Components\TextInput::make('dokter')
                            ->label('Dokter')
                            ->nullable(),
                        Forms\Components\TextInput::make('perawat')
                            ->label('Perawat')
                            ->nullable(),
                        Forms\Components\TextInput::make('pengirim')
                            ->label('Pengirim')
                            ->nullable(),
                        Forms\Components\TextInput::make('nama_peng')
                            ->label('Nama Pengirim')
                            ->nullable(),
                        Forms\Components\TextInput::make('al_kir')
                            ->label('Al Kir')
                            ->nullable(),
                    ]),

                // 12. Detail Rawat Inap & Pulang
                Forms\Components\Section::make('Detail Rawat Inap & Pulang')
                    ->columns(3)
                    ->schema([
                        Forms\Components\DateTimePicker::make('tgl_inap')
                            ->label('Tanggal Rawat Inap')
                            ->native(false)
                            ->nullable(),
                        Forms\Components\TextInput::make('jam_inap')
                            ->label('Jam Rawat Inap')
                            ->nullable(),
                        Forms\Components\DatePicker::make('tgl_pl')
                            ->label('Tanggal Pulang')
                            ->native(false)
                            ->nullable(),
                        Forms\Components\TextInput::make('jam_pl')
                            ->label('Jam Pulang')
                            ->nullable(),
                    ]),

                // 13. Data Tambahan Lain-lain
                Forms\Components\Section::make('Data Tambahan Lain-lain')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('iol')
                            ->label('IOL')
                            ->maxLength(1)
                            ->nullable(),
                        Forms\Components\TextInput::make('gzresp')
                            ->label('GZ Resp')
                            ->required()
                            ->maxLength(1)
                            ->default('T'),
                        Forms\Components\TextInput::make('cek')
                            ->label('Cek')
                            ->numeric()
                            ->nullable(),
                        Forms\Components\TextInput::make('ihs')
                            ->label('IHS')
                            ->maxLength(100)
                            ->nullable(),
                        Forms\Components\TextInput::make('ihs_labmu')
                            ->label('IHS Labmu')
                            ->maxLength(50)
                            ->nullable(),
                        Forms\Components\TextInput::make('no_sep')
                            ->label('No. SEP')
                            ->maxLength(20)
                            ->nullable(),
                        Forms\Components\TextInput::make('reg_sitb')
                            ->label('Reg SITB')
                            ->maxLength(100)
                            ->nullable(),
                        Forms\Components\TextInput::make('tempat')
                            ->label('Tempat')
                            ->maxLength(50)
                            ->nullable(),
                        Forms\Components\TextInput::make('flag_status')
                            ->label('Flag Status')
                            ->numeric()
                            ->nullable(),
                        Forms\Components\Textarea::make('paraf')
                            ->label('Paraf')
                            ->columnSpanFull()
                            ->nullable(),
                        Forms\Components\TextInput::make('bl')
                            ->label('BL')
                            ->nullable(),
                        Forms\Components\TextInput::make('identitas')
                            ->label('Identitas')
                            ->nullable(),
                        Forms\Components\TextInput::make('gol')
                            ->label('Golongan')
                            ->nullable(),
                        Forms\Components\TextInput::make('gawat')
                            ->label('Gawat (Darurat)')
                            ->nullable(),
                        Forms\Components\TextInput::make('pin')
                            ->label('PIN')
                            ->nullable(),
                        Forms\Components\TextInput::make('kkk')
                            ->label('KKK')
                            ->nullable(),
                        Forms\Components\TextInput::make('kdu')
                            ->label('KDU')
                            ->nullable(),
                        Forms\Components\TextInput::make('bbbb')
                            ->label('BBBB')
                            ->nullable(),
                        Forms\Components\TextInput::make('waktu')
                            ->label('Waktu (Lainnya)')
                            ->nullable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_cm')
                    ->label('No. CM')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_pas')
                    ->label('Nama Pasien')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_lahir')
                    ->label('Tgl Lahir')
                    ->date('d/m/Y') // Format tanggal
                    ->sortable(),
                Tables\Columns\TextColumn::make('sex')
                    ->label('JK')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_reg')
                    ->label('No. Registrasi')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_kunj')
                    ->label('Tgl Kunjungan')
                    ->dateTime('d/m/Y H:i') // Format tanggal dan waktu
                    ->sortable(),
                Tables\Columns\TextColumn::make('alamat')
                    ->label('Alamat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telp')
                    ->label('Telp')
                    ->searchable(),
                Tables\Columns\TextColumn::make('asuransi')
                    ->label('Asuransi')
                    ->searchable(),

                // Kolom Waktu (diubah ke time() jika model sudah cast ke H:i:s)
                Tables\Columns\TextColumn::make('jam')
                    ->label('Jam Kunjungan')
                    ->time('H:i:s') // Tampilkan sebagai waktu
                    ->sortable(),
                Tables\Columns\TextColumn::make('jam1')
                    ->label('Jam Kunjungan 1')
                    ->time('H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jam2')
                    ->label('Jam Kunjungan 2')
                    ->time('H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jam_pl')
                    ->label('Jam Pulang')
                    ->time('H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jam_inap')
                    ->label('Jam Inap')
                    ->searchable(),

                // Kolom Boolean (IconColumn)
                Tables\Columns\IconColumn::make('cek_kpsta')
                    ->label('Cek KPSTA')
                    ->boolean(),
                Tables\Columns\IconColumn::make('cek_ktp')
                    ->label('Cek KTP')
                    ->boolean(),
                Tables\Columns\IconColumn::make('cek_kk')
                    ->label('Cek KK')
                    ->boolean(),
                Tables\Columns\IconColumn::make('bank_v_lab')
                    ->label('Bank V Lab')
                    ->boolean(),
                Tables\Columns\IconColumn::make('bank_v_far')
                    ->label('Bank V Far')
                    ->boolean(),
                Tables\Columns\IconColumn::make('bank_v_rad')
                    ->label('Bank V Rad')
                    ->boolean(),
                Tables\Columns\IconColumn::make('bank_v_gz')
                    ->label('Bank V GZ')
                    ->boolean(),
                Tables\Columns\IconColumn::make('bank_v_fis')
                    ->label('Bank V Fis')
                    ->boolean(),
                Tables\Columns\IconColumn::make('flag_mcu')
                    ->label('Flag MCU')
                    ->boolean(),
                Tables\Columns\IconColumn::make('flag_penyakit')
                    ->label('Flag Penyakit')
                    ->boolean(),
                Tables\Columns\IconColumn::make('flag_pasien')
                    ->label('Flag Pasien')
                    ->boolean(),
                Tables\Columns\IconColumn::make('flag_prolanis')
                    ->label('Flag Prolanis')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_hiv')
                    ->label('HIV')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_hbs')
                    ->label('HBS')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_tbc')
                    ->label('TBC')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_pulang')
                    ->label('Pulang')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_titip')
                    ->label('Titip')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_farmasi')
                    ->label('Farmasi')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_radiologi')
                    ->label('Radiologi')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_laboratorium')
                    ->label('Laboratorium')
                    ->boolean(),

                // Kolom lainnya, diatur agar bisa di-toggle atau disembunyikan secara default
                Tables\Columns\TextColumn::make('iol')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kec')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('nama_ortu')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pek_pasien')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pek_ortu')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('alm_ortu')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kelas')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('unit')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pend')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('agama')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('diagnosa')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('bl')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('identitas')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pengirim')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('gol')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('gawat')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('al_kir')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('nama_peng')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pin')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tgl_kunj1')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tgl_kunj2')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('desa')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kls1')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kls2')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kkk')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kdu')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('hub')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kab')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('aktif')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('no_px')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('petugas_tpp')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('dokter')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('perawat')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tgl_pl')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('bbbb')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kd_dx')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('waktu')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('telp_ortu')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kunjungan')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('cara_masuk')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('polisi')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ruang')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tgl_inap')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status_kary')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('gzresp')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('cek')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('id_menikah')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('id_propinsi')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('no_kpsta')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('asal_daerah')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('id_retensi')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('id_alergi')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('fis_asuhan')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('catatan_bpjs')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('nik')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('flag_status')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('paraf')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('id_prop_domisili')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('id_kab_domisili')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('id_kec_domisili')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('id_desa_domisili')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('alamat_domisili')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('lokasi_domisili')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ihs')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('reg_sitb')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('no_sep')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ihs_labmu')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tempat')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Anda bisa menambahkan filter di sini, contoh:
                // Tables\Filters\SelectFilter::make('sex')
                //     ->options([
                //         'L' => 'Laki-laki',
                //         'P' => 'Perempuan',
                //     ])
                //     ->label('Jenis Kelamin'),
                // Tables\Filters\TrashedFilter::make(), // Jika Anda menggunakan Soft Deletes
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(), // Tambahkan View Action untuk melihat detail
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Contoh menambahkan relation manager (Anda perlu membuat relation manager terlebih dahulu)
            // RelationManagers\FarTransactionsRelationManager::class,
            // RelationManagers\BnrCaraRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMasterPasiens::route('/'),
            'create' => Pages\CreateMasterPasien::route('/create'),
            'edit' => Pages\EditMasterPasien::route('/{record}/edit'),
        ];
    }
}
