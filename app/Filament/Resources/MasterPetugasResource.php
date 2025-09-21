<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\MasterPetugas;
use Filament\Resources\Resource;
use App\Models\User; // Import model User
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\MasterPetugasResource\Pages;

class MasterPetugasResource extends Resource
{
    protected static ?string $model = MasterPetugas::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationGroup = 'User Manajemen';
    protected static ?string $pluralModelLabel = 'Master Petugas';
    protected static ?int $navigationSort = 14;
    protected static ?string $modelLabel = 'Petugas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Akun Terhubung & Status')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->live()
                            ->afterStateHydrated(function ($state, Forms\Set $set) {
                                if (! $state) {
                                    return;
                                }

                                $user = User::find($state);

                                if ($user) {
                                    $set('nbm', $user->nbm);
                                    $set('email', $user->email);
                                }
                            })
                            ->afterStateUpdated(function ($state, Set $set) {
                                if (! $state) {
                                    $set('nbm', null);
                                    $set('email', null);
                                    return;
                                }
                                $user = User::find($state);
                                if ($user) {
                                    $set('nbm', $user->nbm);
                                    $set('email', $user->email);
                                }
                            })
                            ->helperText('Pilih akun user yang akan dihubungkan dengan profil petugas ini.'),

                        Forms\Components\TextInput::make('nbm')
                            ->label('NBM')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('email')
                            ->label('Email Akun')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\Toggle::make('biodata_aktif')
                            ->label('Status Aktif')
                            ->default(true)
                            ->helperText('Nonaktifkan jika petugas sudah tidak aktif bekerja.'),
                    ])->columns(2),

                Forms\Components\Tabs::make('Detail Informasi Petugas')->tabs([
                    // Tab untuk Biodata Pribadi
                    Forms\Components\Tabs\Tab::make('Biodata Pribadi')
                        ->icon('heroicon-o-user-circle')
                        ->schema([
                            Forms\Components\TextInput::make('gelar_depan')
                                ->maxLength(15),
                            Forms\Components\TextInput::make('gelar_belakang')
                                ->maxLength(30),
                            Forms\Components\TextInput::make('initial')
                                ->label('Inisial')
                                ->maxLength(2),
                            Forms\Components\TextInput::make('tempat_lahir')
                                ->maxLength(30),
                            Forms\Components\DatePicker::make('tgl_lahir'),
                            Forms\Components\Select::make('jenis_kelamin')
                                ->options([
                                    'Laki-laki' => 'Laki-laki',
                                    'Perempuan' => 'Perempuan'
                                ]),
                            Forms\Components\TextInput::make('golongan_darah')
                                ->maxLength(2),
                            Forms\Components\TextInput::make('nik')->label('NIK')
                                ->maxLength(20),
                            Forms\Components\Select::make('status_menikah')
                                ->label('Status Pernikahan')
                                ->options([
                                    'Sudah' => 'Sudah',
                                    'Belum' => 'Belum'
                                ]),
                            Forms\Components\FileUpload::make('paraf')
                                ->label('Paraf Digital'),
                        ])->columns(2),

                    // Tab untuk Informasi Kepegawaian
                    Forms\Components\Tabs\Tab::make('Informasi Kepegawaian')
                        ->icon('heroicon-o-briefcase')
                        ->schema([
                            Forms\Components\TextInput::make('jabatan')
                                ->label('Jabatan')
                                ->maxLength(40),
                            Forms\Components\TextInput::make('unit')
                                ->label('Unit Kerja')
                                ->maxLength(100),
                            Forms\Components\TextInput::make('sip')
                                ->label('Surat Izin Praktik')->maxLength(30),
                            Forms\Components\TextInput::make('status_karyawan')
                                ->maxLength(20),
                        ])->columns(2),

                    // Tab untuk Kontak & Alamat
                    Forms\Components\Tabs\Tab::make('Kontak & Alamat')
                        ->icon('heroicon-o-map-pin')
                        ->schema([
                            Forms\Components\TextInput::make('telepon')
                                ->label('No. Hp')
                                ->tel()
                                ->maxLength(20),
                            Forms\Components\TextInput::make('id_telegram')->label('ID Telegram'),
                            Forms\Components\TextInput::make('alamat')->maxLength(100),
                            Forms\Components\TextInput::make('nama_kalurahan')->label('Kelurahan/Desa'),
                            Forms\Components\TextInput::make('nama_kecamatan')->label('Kecamatan'),
                            Forms\Components\TextInput::make('nama_propinsi')->label('Provinsi'),
                            Forms\Components\TextInput::make('kode_pos')->maxLength(10),
                        ])->columns(2),

                    // Tab untuk Informasi Finansial
                    Forms\Components\Tabs\Tab::make('Informasi Finansial')
                        ->icon('heroicon-o-banknotes')
                        ->schema([
                            Forms\Components\TextInput::make('npwp')->label('NPWP')->maxLength(50),
                            Forms\Components\TextInput::make('nomor_rekening')
                                ->label('Nomor Rekening')
                                ->maxLength(30),
                            Forms\Components\TextInput::make('nama_rekening')
                                ->label('Nama Pemilik Rekening')->maxLength(50),
                        ])->columns(2),
                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // Menambahkan urutan default berdasarkan nama user
            ->defaultSort('user.name', 'asc')
            ->columns([
                // Menggabungkan nama, nbm, dan email menjadi satu kolom
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Petugas')
                    ->description(fn(MasterPetugas $record): string => "NBM: {$record->user->nbm} | Email: {$record->user->email}")
                    ->searchable(['name', 'nbm', 'email']) // Pencarian bisa melalui ketiga field
                    ->sortable(),

                Tables\Columns\TextColumn::make('jabatan')
                    ->searchable()
                    ->sortable(),

                // DIUBAH: Kolom telepon sekarang terlihat secara default
                Tables\Columns\TextColumn::make('telepon')
                    ->searchable()
                    ->toggleable(), // Terlihat secara default, tapi bisa disembunyikan

                Tables\Columns\IconColumn::make('biodata_aktif')
                    ->label('Status')
                    ->boolean(),

                Tables\Columns\TextColumn::make('unit')
                    ->label('Unit Kerja')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // BARU: Menambahkan kolom alamat
                Tables\Columns\TextColumn::make('alamat')
                    ->searchable()
                    ->limit(30) // Batasi panjang teks yang ditampilkan di tabel
                    ->tooltip(fn(MasterPetugas $record): ?string => $record->alamat) // Tampilkan alamat lengkap saat kursor diarahkan
                    ->toggleable(isToggledHiddenByDefault: true), // Disembunyikan default, bisa diaktifkan dari 'Toggle Columns'
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('biodata_aktif')
                    ->label('Status Aktif')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif'),

                // Filter berdasarkan jabatan
                Tables\Filters\SelectFilter::make('jabatan')
                    ->options(
                        fn(): array => MasterPetugas::query()->whereNotNull('jabatan')->distinct()->pluck('jabatan', 'jabatan')->all()
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMasterPetugas::route('/'),
            'create' => Pages\CreateMasterPetugas::route('/create'),
            'edit' => Pages\EditMasterPetugas::route('/{record}/edit'),
        ];
    }
}
