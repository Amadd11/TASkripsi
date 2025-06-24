<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\MasterPasien;
use App\Models\BenarPendidikan;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BenarPendidikanResource\Pages;
use App\Filament\Resources\BenarPendidikanResource\RelationManagers;

class BenarPendidikanResource extends Resource
{
    protected static ?string $model = BenarPendidikan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Prinsip 12 Benar';
    protected static ?string $pluralModelLabel = 'Benar Pendidikan';
    protected static ?string $modelLabel = 'Benar Pendidikan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Benar Pendidikan/Edukasi')
                    ->description('Pastikan semua data terkait pemberian edukasi kepada pasien sudah benar.')
                    ->schema([
                        Forms\Components\Select::make('no_cm')
                            ->label('Nomor Rekam Medis Pasien')
                            ->relationship(
                                'masterPasien',
                                'no_cm',
                                function ($query) {
                                    $query->whereNotIn('no_cm', function ($subquery) {
                                        $subquery->select('no_cm')->from('bnr_pendidikan');
                                    });
                                }
                            )
                            ->getOptionLabelFromRecordUsing(fn(MasterPasien $record) => "{$record->no_cm} - {$record->nama_pas}") // Menampilkan No. CM dan Nama Pasien
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $pasien = MasterPasien::where('no_cm', $state)->first();
                                $set('no_reg', $pasien?->no_reg);
                            })
                            ->helperText('Pilih nomor rekam medis pasien yang sudah terdaftar.'),
                        Forms\Components\TextInput::make('no_reg')
                            ->label('Nomor Registrasi Transaksi Farmasi')
                            ->disabled(),
                        Forms\Components\Fieldset::make('Verifikasi Edukasi')
                            ->schema([
                                Forms\Components\Toggle::make('is_edukasi')
                                    ->label('Apakah Edukasi sudah diberikan?')
                                    ->hint('Centang jika edukasi kepada pasien sudah diberikan dan didokumentasikan.')
                                    ->required(),
                            ])->columns(1), // Toggle di dalam Fieldset tetap 1 kolom

                        Forms\Components\DatePicker::make('tanggal')
                            ->label('Tanggal')
                            ->required(),
                        Forms\Components\TimePicker::make('jam')
                            ->label('Jam')
                            ->native(false) // Untuk tampilan picker yang lebih baik
                            ->required(),
                        Forms\Components\TextInput::make('id_petugas')
                            ->label('ID Petugas')
                            ->numeric()
                            ->nullable()
                            ->helperText('ID petugas yang bertanggung jawab memberikan edukasi.'),
                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan Tambahan')
                            ->columnSpanFull() // Memastikan textarea mengambil lebar penuh
                            ->rows(3)
                            ->placeholder('Masukkan keterangan tambahan terkait edukasi yang diberikan.'),
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
                Tables\Columns\IconColumn::make('is_edukasi')
                    ->label('Edukasi Diberikan')
                    ->boolean(),
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
                Tables\Filters\TernaryFilter::make('is_edukasi')
                    ->label('Filter Edukasi Diberikan'),
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
            'index' => Pages\ListBenarPendidikans::route('/'),
            'create' => Pages\CreateBenarPendidikan::route('/create'),
            'edit' => Pages\EditBenarPendidikan::route('/{record}/edit'),
        ];
    }
}
