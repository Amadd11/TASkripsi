<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\MasterPasien;
use App\Models\BenarEvaluasi;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BenarEvaluasiResource\Pages;
use App\Filament\Resources\BenarEvaluasiResource\RelationManagers;

class BenarEvaluasiResource extends Resource
{
    protected static ?string $model = BenarEvaluasi::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-magnifying-glass';
    protected static ?string $pluralModelLabel = 'Benar Evaluasi';
    protected static ?string $modelLabel = 'Benar Evaluasi';
    protected static ?string $navigationGroup = 'Hasil Pemeriksaan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Benar Dokumentasi')
                    ->description('Pastikan semua data terkait dokumentasi sudah benar.')
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
                            ->label('Nomor Registrasi')
                            ->disabled(),
                        Forms\Components\Fieldset::make('Aspek Evaluasi')
                            ->schema([
                                Forms\Components\Toggle::make('is_efek_samping')
                                    ->label('Apakah ada Efek Samping?')
                                    ->hint('Centang jika ada efek samping yang didokumentasikan.')
                                    ->required(),
                                Forms\Components\Toggle::make('is_alergi')
                                    ->label('Apakah ada Reaksi Alergi?')
                                    ->hint('Centang jika ada reaksi alergi yang didokumentasikan.')
                                    ->required(),
                                Forms\Components\Toggle::make('is_efek_terapi')
                                    ->label('Apakah Efek Terapi tercapai?')
                                    ->hint('Centang jika efek terapi yang diharapkan tercapai.')
                                    ->required(),
                            ])->columns(1), // Toggles di dalam Fieldset tetap 1 kolom
                        Forms\Components\DatePicker::make('tanggal')
                            ->label('Tanggal')
                            ->native(false)
                            ->required(),
                        Forms\Components\TimePicker::make('jam')
                            ->label('Jam')
                            ->required(),
                        Forms\Components\TextInput::make('id_petugas')
                            ->label('ID Petugas')
                            ->minValue(0)
                            ->numeric()
                            ->helperText('ID petugas yang bertanggung jawab.'),
                        Forms\Components\TextInput::make('is_no_reg')
                            ->label('Nomor Registrasi Internal')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('Nomor registrasi internal untuk pencatatan.'),
                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan Tambahan')
                            ->columnSpanFull() // Memastikan textarea mengambil lebar penuh
                            ->rows(3)
                            ->placeholder('Masukkan keterangan tambahan terkait hasil evaluasi.'),


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
                Tables\Columns\IconColumn::make('is_efek_samping')
                    ->label('Efek Samping')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_alergi')
                    ->label('Reaksi Alergi')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_efek_terapi')
                    ->label('Efek Terapi')
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
                Tables\Filters\TernaryFilter::make('is_efek_samping')
                    ->label('Filter Efek Samping'),
                Tables\Filters\TernaryFilter::make('is_alergi')
                    ->label('Filter Reaksi Alergi'),
                Tables\Filters\TernaryFilter::make('is_efek_terapi')
                    ->label('Filter Efek Terapi Tercapai'),
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
            'index' => Pages\ListBenarEvaluasis::route('/'),
            'create' => Pages\CreateBenarEvaluasi::route('/create'),
            'edit' => Pages\EditBenarEvaluasi::route('/{record}/edit'),
        ];
    }
}
