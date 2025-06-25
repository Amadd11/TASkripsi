<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\MasterPasien;
use App\Models\BenarDokumentasi;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BenarDokumentasiResource\Pages;
use App\Filament\Resources\BenarDokumentasiResource\RelationManagers;

class BenarDokumentasiResource extends Resource
{
    protected static ?string $model = BenarDokumentasi::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';
    protected static ?string $navigationGroup = 'Prinsip 12 Benar';
    protected static ?string $pluralModelLabel = 'Benar Dokumentasi';
    protected static ?string $modelLabel = 'Benar Dokumentasi';


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
                        Forms\Components\Fieldset::make('Verifikasi Dokumentasi')
                            ->schema([
                                Forms\Components\Toggle::make('is_pasien')
                                    ->label('Apakah Dokumentasi Pasien Benar?')
                                    ->hint('Centang jika informasi pasien didokumentasikan dengan benar.')
                                    ->required(),
                                Forms\Components\Toggle::make('is_dosis')
                                    ->label('Apakah Dokumentasi Dosis Benar?')
                                    ->hint('Centang jika dosis didokumentasikan dengan benar.')
                                    ->required(),
                                Forms\Components\Toggle::make('is_obat')
                                    ->label('Apakah Dokumentasi Obat Benar?')
                                    ->hint('Centang jika nama obat didokumentasikan dengan benar.')
                                    ->required(),
                                Forms\Components\Toggle::make('is_waktu')
                                    ->label('Apakah Dokumentasi Waktu Pemberian Benar?')
                                    ->hint('Centang jika waktu pemberian didokumentasikan dengan benar.')
                                    ->required(),
                                Forms\Components\Toggle::make('is_rute')
                                    ->label('Apakah Dokumentasi Rute Pemberian Benar?')
                                    ->hint('Centang jika rute pemberian didokumentasikan dengan benar.')
                                    ->required(),
                            ])->columns(1),
                        Forms\Components\TextInput::make('is_no_reg')
                            ->label('Nomor Registrasi Internal')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('Nomor registrasi internal untuk pencatatan.'),
                        Forms\Components\DatePicker::make('tanggal')
                            ->label('Tanggal')
                            ->required(),
                        Forms\Components\TimePicker::make('jam')
                            ->label('Jam')
                            ->required(),
                        Forms\Components\TextInput::make('id_petugas')
                            ->label('ID Petugas')
                            ->numeric()
                            ->nullable()
                            ->helperText('ID petugas yang melakukan dokumentasi.'),             // Mengelompokkan Toggles untuk penampilan yang rapi
                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan Tambahan')
                            ->columnSpanFull()
                            ->rows(3),
                    ])->columns(2),
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
                Tables\Columns\IconColumn::make('is_pasien')
                    ->label('Pasien Benar')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_dosis')
                    ->label('Dosis Benar')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_obat')
                    ->label('Obat Benar')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_waktu')
                    ->label('Waktu Benar')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_rute')
                    ->label('Rute Benar')
                    ->boolean(),
                Tables\Columns\TextColumn::make('is_no_reg')
                    ->label('No. Reg Internal')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
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
                Tables\Filters\TernaryFilter::make('is_pasien')
                    ->label('Filter Dokumentasi Pasien Benar'),
                Tables\Filters\TernaryFilter::make('is_dosis')
                    ->label('Filter Dokumentasi Dosis Benar'),
                Tables\Filters\TernaryFilter::make('is_obat')
                    ->label('Filter Dokumentasi Obat Benar'),
                Tables\Filters\TernaryFilter::make('is_waktu')
                    ->label('Filter Dokumentasi Waktu Benar'),
                Tables\Filters\TernaryFilter::make('is_rute')
                    ->label('Filter Dokumentasi Rute Benar'),
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
            'index' => Pages\ListBenarDokumentasis::route('/'),
            'create' => Pages\CreateBenarDokumentasi::route('/create'),
            'edit' => Pages\EditBenarDokumentasi::route('/{record}/edit'),
        ];
    }
}
