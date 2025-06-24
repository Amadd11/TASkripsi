<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\MasterPasien;
use App\Models\FarTransaction;
use Filament\Resources\Resource;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\FarTransactionResource\Pages;
use Filament\Forms\Components\Section; // Import Section
use Filament\Forms\Components\TimePicker; // Import TimePicker
use App\Filament\Resources\FarTransactionResource\RelationManagers;

class FarTransactionResource extends Resource
{
    protected static ?string $model = FarTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar'; // Icon yang lebih relevan

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Umum Transaksi')
                    ->description('Data dasar transaksi farmasi.')
                    ->schema([
                        Forms\Components\Select::make('no_cm')
                            ->label('Nomor CM (Pasien)')
                            ->relationship(
                                'masterPasien',
                                'no_cm',
                                modifyQueryUsing: function ($query) {
                                    $query->whereNotIn('no_cm', function ($subquery) {
                                        $subquery->select('no_cm')->from('far_transactions');
                                    });
                                }
                            ) // Pastikan relasi ini ada di model transaksi Anda
                            ->searchable()
                            ->preload()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Cek apakah no_cm sudah ada di data transaksi
                                $exists = FarTransaction::where('no_cm', $state)->exists();

                                if ($exists) {
                                    Notification::make()
                                        ->title('Perhatian')
                                        ->body('Nomor CM ini sudah pernah digunakan dalam transaksi lain.')
                                        ->danger()
                                        ->persistent()
                                        ->send();
                                }

                                // Ambil data pasien dan isi otomatis
                                $pasien = MasterPasien::where('no_cm', $state)->first();

                                if ($pasien) {
                                    $set('no_reg', $pasien->no_reg);
                                    $set('nama_pas', $pasien->nama_pas);
                                    $set('alamat', $pasien->alamat);
                                    $set('tgl_lahir', $pasien->tgl_lahir);
                                    $set('sex', $pasien->sex);
                                    $set('kelas', $pasien->kelas);
                                    $set('asuransi', $pasien->asuransi);
                                }
                            })
                            ->required()
                            ->live() // Penting agar afterStateUpdated berjalan secara real-time
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('no_reg')
                            ->label('Nomor Registrasi')
                            ->maxLength(8)
                            ->nullable()
                            ->disabled() // Field ini otomatis diisi, jadi dinonaktifkan
                            ->columnSpan(1),
                        Forms\Components\DateTimePicker::make('tgl')
                            ->label('Tanggal Transaksi')
                            ->native(false)
                            ->nullable()
                            ->columnSpan(1),
                        Forms\Components\TimePicker::make('jam')
                            ->label('Jam Transaksi')
                            ->native(false)
                            ->nullable()
                            ->columnSpan(1),
                        Forms\Components\Select::make('unit')
                            ->label('Unit')
                            ->options([
                                'Poli Umum' => 'Poli Umum',
                                'Farmasi' => 'Farmasi',
                                'UGD' => 'UGD',
                                // Tambahkan opsi lain dari tabel master unit Anda
                            ])
                            ->searchable()
                            ->nullable()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('dokter')
                            ->label('Dokter')
                            ->maxLength(30)
                            ->nullable()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('petugas')
                            ->label('Petugas')
                            ->maxLength(30)
                            ->nullable()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('sampel')
                            ->label('Sampel')
                            ->maxLength(30)
                            ->nullable()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('pengirim')
                            ->label('Pengirim')
                            ->maxLength(30)
                            ->nullable()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('asuransi')
                            ->label('Asuransi')
                            ->maxLength(30)
                            ->disabled()
                            ->nullable()
                            ->columnSpan(1),
                    ])->columns(3),

                Forms\Components\Section::make('Informasi Pasien Lainnya')
                    ->description('Data pasien yang diisi otomatis berdasarkan Nomor CM.')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('nama_pas')
                            ->label('Nama Pasien')
                            ->maxLength(30)
                            ->nullable()
                            ->disabled(), // Field ini otomatis diisi, jadi dinonaktifkan
                        Forms\Components\TextInput::make('alamat')
                            ->label('Alamat Pasien')
                            ->maxLength(50)
                            ->nullable()
                            ->disabled(), // Field ini otomatis diisi, jadi dinonaktifkan
                        Forms\Components\DateTimePicker::make('tgl_lahir')
                            ->label('Tanggal Lahir Pasien')
                            ->native(false)
                            ->nullable()
                            ->disabled(), // Field ini otomatis diisi, jadi dinonaktifkan
                        Forms\Components\Select::make('sex')
                            ->label('Jenis Kelamin Pasien')
                            ->options([
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            ])
                            ->nullable()
                            ->disabled(), // Field ini otomatis diisi, jadi dinonaktifkan
                        Forms\Components\TextInput::make('kelas')
                            ->label('Kelas Pasien')
                            ->maxLength(5)
                            ->nullable()
                            ->disabled(), // Field ini otomatis diisi, jadi dinonaktifkan
                        Forms\Components\TextInput::make('iol')
                            ->label('IOL')
                            ->maxLength(1)
                            ->nullable(),
                        Forms\Components\TextInput::make('rujuk')
                            ->label('Rujuk')
                            ->maxLength(20)
                            ->nullable(),
                        Forms\Components\TextInput::make('bl_kunj')
                            ->label('BL Kunjungan')
                            ->maxLength(1)
                            ->nullable(),
                        Forms\Components\Select::make('shift')
                            ->label('Shift')
                            ->options([
                                'P' => 'Pagi',
                                'S' => 'Siang',
                                'M' => 'Malam',
                            ])
                            ->nullable(),
                        Forms\Components\TextInput::make('no_psn')
                            ->label('No. PSN')
                            ->numeric()
                            ->nullable(),
                        Forms\Components\TextInput::make('loket')
                            ->label('Loket')
                            ->numeric()
                            ->nullable(),
                    ]),

                Forms\Components\Section::make('Detail Keuangan')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('biaya')
                            ->label('Biaya')
                            ->numeric()
                            ->nullable(),
                        Forms\Components\TextInput::make('biaya_pns')
                            ->label('Biaya PNS')
                            ->numeric()
                            ->nullable(),
                        Forms\Components\TextInput::make('grand_total')
                            ->label('Grand Total')
                            ->numeric()
                            ->nullable(),
                        Forms\Components\TextInput::make('bayar')
                            ->label('Bayar')
                            ->numeric()
                            ->nullable(),
                        Forms\Components\Select::make('lunas')
                            ->label('Status Lunas')
                            ->options([
                                'BELUM' => 'BELUM',
                                'LUNAS' => 'LUNAS',
                            ])
                            ->required()
                            ->default('BELUM'),
                        Forms\Components\TextInput::make('bagian')
                            ->label('Bagian')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('gp')
                            ->label('GP')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('emr')
                            ->label('EMR')
                            ->numeric()
                            ->nullable(),
                        Forms\Components\TextInput::make('bpjs')
                            ->label('BPJS')
                            ->numeric()
                            ->nullable(),
                    ]),

                Forms\Components\Section::make('Informasi Racikan & Embalase')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('racikan')
                            ->label('Racikan')
                            ->numeric()
                            ->nullable(),
                        Forms\Components\TextInput::make('sub_embalase')
                            ->label('Sub Embalase')
                            ->numeric()
                            ->nullable(),
                        Forms\Components\TextInput::make('sub_er')
                            ->label('Sub ER')
                            ->numeric()
                            ->nullable(),
                        Forms\Components\TextInput::make('sub_racikan')
                            ->label('Sub Racikan')
                            ->numeric()
                            ->nullable(),
                        Forms\Components\TextInput::make('sub_item_er')
                            ->label('Sub Item ER')
                            ->numeric()
                            ->nullable(),
                    ]),

                Forms\Components\Section::make('Clinical Review (TR_)')
                    ->description('Verifikasi data resep dan potensi masalah klinis.')
                    ->columns(3)
                    ->schema([
                        Forms\Components\Toggle::make('tr_Jelas')
                            ->label('Resep Jelas')
                            ->default(0),
                        Forms\Components\Toggle::make('tr_obat')
                            ->label('Obat Sesuai')
                            ->default(0),
                        Forms\Components\Toggle::make('tr_dosis')
                            ->label('Dosis Sesuai')
                            ->default(0),
                        Forms\Components\Toggle::make('tr_rute')
                            ->label('Rute Sesuai')
                            ->default(0),
                        Forms\Components\Toggle::make('tr_waktu')
                            ->label('Waktu Sesuai')
                            ->default(0),
                        Forms\Components\Toggle::make('tr_duplikasi')
                            ->label('Tidak Ada Duplikasi')
                            ->default(0),
                        Forms\Components\Toggle::make('tr_interaksi')
                            ->label('Tidak Ada Interaksi')
                            ->default(0),
                        Forms\Components\Toggle::make('tr_kontradiksi')
                            ->label('Tidak Ada Kontradiksi')
                            ->default(0),
                        Forms\Components\TextInput::make('tr_petugas')
                            ->label('Technical Review Petugas')
                            ->numeric()
                            ->nullable(),
                        Forms\Components\Textarea::make('tr_lanjut')
                            ->label('Catatan Lanjutan (TR)')
                            ->maxLength(250)
                            ->columnSpanFull()
                            ->nullable(),
                    ]),

                Forms\Components\Section::make('Clinical Review (TO_)')
                    ->description('Verifikasi data penyiapan dan output obat.')
                    ->columns(3)
                    ->schema([
                        Forms\Components\Toggle::make('to_identitas')
                            ->label('Identitas Pasien Sesuai')
                            ->default(0),
                        Forms\Components\Toggle::make('to_obat')
                            ->label('Nama Obat Sesuai')
                            ->default(0),
                        Forms\Components\Toggle::make('to_jumlah')
                            ->label('Jumlah Obat Sesuai')
                            ->default(0),
                        Forms\Components\Toggle::make('to_waktu')
                            ->label('Waktu Penyiapan Sesuai')
                            ->default(0),
                        Forms\Components\Toggle::make('to_rute')
                            ->label('Rute Penyiapan Sesuai')
                            ->default(0),
                        Forms\Components\TextInput::make('to_petugas')
                            ->label('Technical Output Petugas')
                            ->numeric()
                            ->nullable(),
                        Forms\Components\Textarea::make('to_lanjut')
                            ->label('Catatan Lanjutan (TO)')
                            ->maxLength(250)
                            ->columnSpanFull()
                            ->nullable(),
                    ]),

                Forms\Components\Section::make('Flags Lain-lain')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('id_h_cp')
                            ->label('ID H CP')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('klinik_online')
                            ->label('Klinik Online')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('f_terapi_plg')
                            ->label('F Terapi PLG')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Forms\Components\Toggle::make('is_gofar')
                            ->label('Is GoFar')
                            ->default(0),
                        Forms\Components\Toggle::make('is_dt')
                            ->label('Is DT')
                            ->default(0),
                        Forms\Components\Toggle::make('is_onl')
                            ->label('Is Online')
                            ->default(0),
                        Forms\Components\Toggle::make('is_kronis')
                            ->label('Is Kronis')
                            ->default(0),
                        Forms\Components\Textarea::make('catatan')
                            ->label('Catatan Transaksi')
                            ->maxLength(200)
                            ->columnSpanFull()
                            ->nullable(),
                        Forms\Components\TextInput::make('cetak')
                            ->label('Cetak')
                            ->maxLength(5)
                            ->nullable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_reg')
                    ->label('No. Registrasi')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('masterPasien.no_cm') // Mengambil no_cm dari relasi
                    ->label('No. CM Pasien') // Label baru
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('masterPasien.nama_pas')
                    ->label('Nama Pasien')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('tgl')
                    ->label('Tanggal Transaksi')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jam')
                    ->label('Jam Transaksi')
                    ->time('H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('grand_total')
                    ->label('Total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('lunas')
                    ->label('Lunas')
                    ->searchable()
                    ->badge() // Tampilkan sebagai badge untuk status
                    ->color(fn(string $state): string => match ($state) {
                        'BELUM' => 'danger',
                        'LUNAS' => 'success',
                        default => 'gray',
                    }),

                // Kolom Boolean (IconColumn)
                Tables\Columns\IconColumn::make('vlag_saji')
                    ->label('Saji')
                    ->boolean(),
                Tables\Columns\IconColumn::make('tr_Jelas')
                    ->label('TR Jelas')
                    ->boolean(),
                Tables\Columns\IconColumn::make('tr_obat')
                    ->label('TR Obat')
                    ->boolean(),
                Tables\Columns\IconColumn::make('tr_dosis')
                    ->label('TR Dosis')
                    ->boolean(),
                Tables\Columns\IconColumn::make('tr_rute')
                    ->label('TR Rute')
                    ->boolean(),
                Tables\Columns\IconColumn::make('tr_waktu')
                    ->label('TR Waktu')
                    ->boolean(),
                Tables\Columns\IconColumn::make('tr_duplikasi')
                    ->label('TR Duplikasi')
                    ->boolean(),
                Tables\Columns\IconColumn::make('tr_interaksi')
                    ->label('TR Interaksi')
                    ->boolean(),
                Tables\Columns\IconColumn::make('tr_kontradiksi')
                    ->label('TR Kontradiksi')
                    ->boolean(),
                Tables\Columns\IconColumn::make('to_identitas')
                    ->label('TO Identitas')
                    ->boolean(),
                Tables\Columns\IconColumn::make('to_obat')
                    ->label('TO Obat')
                    ->boolean(),
                Tables\Columns\IconColumn::make('to_jumlah')
                    ->label('TO Jumlah')
                    ->boolean(),
                Tables\Columns\IconColumn::make('to_waktu')
                    ->label('TO Waktu')
                    ->boolean(),
                Tables\Columns\IconColumn::make('to_rute')
                    ->label('TO Rute')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_gofar')
                    ->label('GoFar')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_dt')
                    ->label('DT')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_onl')
                    ->label('Online')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_kronis')
                    ->label('Kronis')
                    ->boolean(),

                // Kolom lainnya yang disembunyikan secara default
                Tables\Columns\TextColumn::make('unit')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('dokter')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('petugas')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('sampel')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pengirim')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('biaya')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('cetak')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tgl_lahir')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('alamat')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('sex')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kelas')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('iol')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('rujuk')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('bl_kunj')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('shift')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('no_psn')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('asuransi')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('biaya_pns')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tgl_ambil')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('menit')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('panggil')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('racikan')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('bpjs')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('catatan')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('loket')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('sub_embalase')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('sub_er')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('sub_racikan')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('sub_item_er')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('bayar')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('emr')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('bagian')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('gp')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('id_h_cp')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('klinik_online')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('f_terapi_plg')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('no_tunggu')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tanggal_saji')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('jam_saji')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tr_lanjut')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('to_lanjut')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tr_petugas')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('to_petugas')
                    ->numeric()
                    ->sortable()
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
                // Contoh filter untuk status lunas
                Tables\Filters\SelectFilter::make('lunas')
                    ->options([
                        'BELUM' => 'Belum Lunas',
                        'LUNAS' => 'Sudah Lunas',
                    ])
                    ->label('Status Lunas'),
                Tables\Filters\Filter::make('tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Dari Tanggal')
                            ->native(false),
                        Forms\Components\DatePicker::make('until')
                            ->label('Sampai Tanggal')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tgl', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tgl', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(), // Tambahkan View Action
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
            // Tambahkan relasi manager di sini jika Anda ingin melihat data pasien
            // terkait atau data bnr_ terkait langsung dari halaman transaksi.
            // Contoh: RelationManagers\MasterPasienRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFarTransactions::route('/'),
            'create' => Pages\CreateFarTransaction::route('/create'),
            'edit' => Pages\EditFarTransaction::route('/{record}/edit'),
        ];
    }
}
