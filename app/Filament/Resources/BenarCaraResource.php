<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\BenarCara;
use Filament\Tables\Table;
use App\Models\MasterPasien;
use App\Models\FarTransaction;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BenarCaraResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BenarCaraResource\RelationManagers;
use App\Filament\Widgets\BenarCaraStats;
use App\Filament\Widgets\BenarHakClientStats;
use App\Filament\Widgets\PersentaseKepatuhan\BenarCaraPercentageStats;

class BenarCaraResource extends Resource
{
    protected static ?string $model = BenarCara::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $pluralModelLabel = 'Benar Cara Pemberian';

    protected static ?string $modelLabel = 'Benar Cara Pemberian';




    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Benar Cara')
                    ->description('Pastikan semua data terkait cara pemberian sudah benar.')
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
                        // Menonaktifkan field ini di halaman Edit agar tidak bisa diubah
                        // ->disabled(fn(string $operation): bool => $operation === 'edit'),
                        Forms\Components\TextInput::make('no_reg')
                            ->label('Nomor Registrasi')
                            ->disabled(),
                        Forms\Components\Fieldset::make('Verifikasi Cara')
                            ->schema([
                                Forms\Components\Toggle::make('is_oral')
                                    ->label('Apakah Pemberian Oral?')
                                    ->hint('Centang jika cara pemberian adalah oral (melalui mulut).')
                                    ->required(),
                                Forms\Components\Toggle::make('is_iv')
                                    ->label('Apakah Pemberian Intravena (IV)?')
                                    ->hint('Centang jika cara pemberian adalah intravena (melalui pembuluh darah).')
                                    ->required(),
                                Forms\Components\Toggle::make('is_im')
                                    ->label('Apakah Pemberian Intramuskular (IM)?')
                                    ->hint('Centang jika cara pemberian adalah intramuskular (melalui otot).')
                                    ->required(),
                            ])->columns(1),
                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan Tambahan')
                            ->placeholder('Masukkan keterangan tambahan terkait cara pemberian.')
                            ->columnSpanFull()
                            ->rows(3),
                    ])->columns(2),
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Menampilkan nilai dari relasi untuk kolom no_reg dan no_cm
                Tables\Columns\TextColumn::make('masterPasien.no_cm')
                    ->label('No. CM Pasien')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('masterPasien.no_reg')
                    ->label('No. Reg Transaksi')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_oral')
                    ->label('Oral')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_iv')
                    ->label('Intravena')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_im')
                    ->label('Intramuskular')
                    ->boolean(),
                Tables\Columns\TextColumn::make('is_no_reg')
                    ->label('No. Reg Internal')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false), // Default ditampilkan
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y') // Format tanggal yang lebih mudah dibaca
                    ->sortable(),
                Tables\Columns\TextColumn::make('jam')
                    ->label('Jam')
                    ->time('H:i') // Format jam yang lebih mudah dibaca
                    ->sortable(),
                Tables\Columns\TextColumn::make('id_petugas')
                    ->label('ID Petugas')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(50) // Batasi panjang teks untuk tampilan tabel
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
                Tables\Filters\TernaryFilter::make('is_oral')
                    ->label('Filter Oral'),
                Tables\Filters\TernaryFilter::make('is_iv')
                    ->label('Filter Intravena'),
                Tables\Filters\TernaryFilter::make('is_im')
                    ->label('Filter Intramuskular'),
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
            'index' => Pages\ListBenarCaras::route('/'),
            'create' => Pages\CreateBenarCara::route('/create'),
            'edit' => Pages\EditBenarCara::route('/{record}/edit'),
        ];
    }
}
