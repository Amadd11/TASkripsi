<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\MasterPasien;
use Filament\Resources\Resource;
use App\Models\BenarReaksiMakanan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BenarReaksiMakananResource\Pages;
use App\Filament\Resources\BenarReaksiMakananResource\RelationManagers;

class BenarReaksiMakananResource extends Resource
{
    protected static ?string $model = BenarReaksiMakanan::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';
    protected static ?string $pluralModelLabel = 'Benar Reaksi Makanan';
    protected static ?string $modelLabel = 'Benar Reaksi Makanan';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Benar Reaksi Makanan')
                    ->description('Pastikan semua data terkait reaksi makanan pasien sudah benar.')
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
                        Forms\Components\Fieldset::make('Verifikasi Reaksi Makanan')
                            ->schema([
                                Forms\Components\Toggle::make('is_efek_makanan')
                                    ->label('Apakah ada Efek Reaksi Makanan?')
                                    ->hint('Centang jika ada efek reaksi makanan yang didokumentasikan.')
                                    ->required(),
                            ])->columns(1), // Toggle di dalam Fieldset tetap 1 kolom
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
                            ->placeholder('Masukkan keterangan tambahan terkait reaksi makanan.'),

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
                Tables\Columns\IconColumn::make('is_efek_makanan')
                    ->label('Efek Reaksi Makanan')
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
                Tables\Filters\TernaryFilter::make('is_efek_makanan')
                    ->label('Filter Efek Reaksi Makanan'),
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
            'index' => Pages\ListBenarReaksiMakanans::route('/'),
            'create' => Pages\CreateBenarReaksiMakanan::route('/create'),
            'edit' => Pages\EditBenarReaksiMakanan::route('/{record}/edit'),
        ];
    }
}
