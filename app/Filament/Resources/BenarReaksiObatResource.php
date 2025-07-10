<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\MasterPasien;
use App\Models\BenarReaksiObat;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BenarReaksiObatResource\Pages;
use App\Filament\Resources\BenarReaksiObatResource\RelationManagers;

class BenarReaksiObatResource extends Resource
{
    protected static ?string $model = BenarReaksiObat::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';
    protected static ?string $pluralModelLabel = 'Benar Reaksi Obat';
    protected static ?string $modelLabel = 'Benar Reaksi Obat';
    protected static ?string $navigationGroup = 'Hasil Pemeriksaan';
    protected static ?int $navigationSort = 9;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Benar Reaksi Obat')
                    ->description('Pastikan semua data terkait reaksi obat dan efek terapi sudah benar.')
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
                        Forms\Components\Fieldset::make('Verifikasi Reaksi Obat & Efek Terapi')
                            ->schema([
                                Forms\Components\Toggle::make('is_efek_samping')
                                    ->label('Apakah ada Efek Samping?')
                                    ->hint('Centang jika ada efek samping obat yang didokumentasikan.')
                                    ->required(),
                                Forms\Components\Toggle::make('is_alergi')
                                    ->label('Apakah ada Reaksi Alergi?')
                                    ->hint('Centang jika ada reaksi alergi obat yang didokumentasikan.')
                                    ->required(),
                                Forms\Components\Toggle::make('is_efek_terapi')
                                    ->label('Apakah Efek Terapi tercapai?')
                                    ->hint('Centang jika efek terapi yang diharapkan dari obat tercapai.')
                                    ->required(),
                            ])->columns(1), 
                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan Tambahan')
                            ->columnSpanFull() // Memastikan textarea mengambil lebar penuh
                            ->rows(3)
                            ->placeholder('Masukkan keterangan tambahan terkait reaksi obat atau efek terapi.'),
                    ])->columns(2), // Mengatur layout form menjadi 2 kolom
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('petugas.name')
                    ->label('Petugas')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('masterPasien.no_cm')
                    ->label('No. CM Pasien')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('masterPasien.no_reg')
                    ->label('No. Registrasi')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('masterPasien.nama_pas')
                    ->label('Nama Pasien')
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
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jam')
                    ->label('Jam')
                    ->time('H:i')
                    ->sortable(),
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
            'index' => Pages\ListBenarReaksiObats::route('/'),
            'create' => Pages\CreateBenarReaksiObat::route('/create'),
            'edit' => Pages\EditBenarReaksiObat::route('/{record}/edit'),
        ];
    }
}
