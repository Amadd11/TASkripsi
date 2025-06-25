<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\BenarObat;
use Filament\Tables\Table;
use App\Models\MasterPasien;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BenarObatResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BenarObatResource\RelationManagers;

class BenarObatResource extends Resource
{
    protected static ?string $model = BenarObat::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box'; // Ikon navigasi yang relevan untuk obat
    protected static ?string $navigationGroup = 'Prinsip 12 Benar';
    protected static ?string $pluralModelLabel = 'Benar Obat';
    protected static ?string $modelLabel = 'Benar Obat';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Benar Obat')
                    ->description('Pastikan semua data terkait pemberian obat sudah benar.')
                    ->schema([
                        Forms\Components\Select::make('no_cm')
                            ->label('Nomor Rekam Medis Pasien')
                            ->relationship('masterPasien', 'no_cm')
                            ->getOptionLabelFromRecordUsing(fn(MasterPasien $record) => "{$record->no_cm} - {$record->nama_pas}") // Menampilkan No. CM dan Nama Pasien
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $pasien = MasterPasien::where('no_cm', $state)->first();
                                $set('no_reg', $pasien?->no_reg);
                            })
                            ->afterStateHydrated(function ($state, callable $set) {
                                if ($state) {
                                    $pasien = MasterPasien::where('no_cm', $state)->first();
                                    $set('no_reg', $pasien?->no_reg);
                                }
                            })
                            ->helperText('Pilih nomor rekam medis pasien yang sudah terdaftar.'),
                        Forms\Components\TextInput::make('no_reg')
                            ->label('Nomor Registrasi Transaksi Farmasi')
                            ->disabled(),
                        Forms\Components\Fieldset::make('Verifikasi Obat')
                            ->schema([
                                Forms\Components\Toggle::make('is_nama_obat')
                                    ->label('Apakah Nama Obat Benar?')
                                    ->hint('Centang jika nama obat yang diberikan sudah sesuai.')
                                    ->required(),
                                Forms\Components\Toggle::make('is_label')
                                    ->label('Apakah Label Obat Benar?')
                                    ->hint('Centang jika label obat sudah sesuai dengan resep.')
                                    ->required(),
                                Forms\Components\Toggle::make('is_resep')
                                    ->label('Apakah Resep Benar?')
                                    ->hint('Centang jika resep sudah diverifikasi kebenarannya.')
                                    ->required(),
                            ])->columns(1), // Toggles di dalam Fieldset tetap 1 kolom

                        Forms\Components\TextInput::make('is_no_reg')
                            ->label('Nomor Registrasi Internal')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('Nomor registrasi internal untuk pencatatan.')
                            ->columnSpan(1), // Memastikan mengambil 1 kolom penuh

                        Forms\Components\DatePicker::make('tanggal')
                            ->label('Tanggal')
                            ->native(false) // Untuk tampilan picker yang lebih baik
                            ->required(),
                        Forms\Components\TimePicker::make('jam')
                            ->label('Jam')
                            ->required(),
                        Forms\Components\TextInput::make('id_petugas')
                            ->label('ID Petugas')
                            ->numeric()
                            ->nullable()
                            ->helperText('ID petugas yang bertanggung jawab.'),
                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan Tambahan')
                            ->columnSpanFull() // Memastikan textarea mengambil lebar penuh
                            ->rows(3)
                            ->placeholder('Masukkan keterangan tambahan terkait verifikasi obat.'),

                    ])->columns(2), // Mengatur layout form menjadi 2 kolom
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('masterPasien.no_cm')
                    ->label('No. CM Pasien')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('masterPasien.no_reg')
                    ->label('No. Reg Transaksi')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_nama_obat')
                    ->label('Nama Obat Benar')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_label')
                    ->label('Label Benar')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_resep')
                    ->label('Resep Benar')
                    ->boolean(),
                Tables\Columns\TextColumn::make('is_no_reg')
                    ->label('No. Reg Internal')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jam')
                    ->label('Jam')
                    ->time('H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('id_petugas')
                    ->label('ID Petugas')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime('d M Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_nama_obat')
                    ->label('Filter Nama Obat Benar'),
                Tables\Filters\TernaryFilter::make('is_label')
                    ->label('Filter Label Obat Benar'),
                Tables\Filters\TernaryFilter::make('is_resep')
                    ->label('Filter Resep Benar'),
            ])
            ->actions([
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
            'index' => Pages\ListBenarObats::route('/'),
            'create' => Pages\CreateBenarObat::route('/create'),
            'edit' => Pages\EditBenarObat::route('/{record}/edit'),
        ];
    }
}
