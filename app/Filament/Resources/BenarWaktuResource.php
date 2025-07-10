<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\BenarWaktu;
use Filament\Tables\Table;
use App\Models\MasterPasien;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BenarWaktuResource\Pages;
use Filament\Forms\Components\Fieldset; // Import Fieldset
use App\Filament\Resources\BenarWaktuResource\RelationManagers;
use Filament\Tables\Filters\TernaryFilter; // Import TernaryFilter

class BenarWaktuResource extends Resource
{
    protected static ?string $model = BenarWaktu::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $pluralModelLabel = 'Benar Waktu';
    protected static ?string $modelLabel = 'Benar Waktu';
    protected static ?string $navigationGroup = 'Hasil Pemeriksaan';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Benar Waktu')
                    ->description('Pastikan semua data terkait waktu sudah benar.')
                    ->schema([
                        Forms\Components\Select::make('no_cm')
                            ->label('Nomor Rekam Medis Pasien')
                            ->relationship('masterPasien', 'no_cm')
                            ->getOptionLabelFromRecordUsing(fn(MasterPasien $record) => "{$record->no_cm} - {$record->nama_pas}")
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
                        Forms\Components\Fieldset::make('Verifikasi Waktu Pemberian')->schema([

                            Forms\Components\Toggle::make('waktu_is_pagi')
                                ->label('Pagi (06:00 - 08:00)')
                                ->live()
                                ->afterStateUpdated(
                                    fn(Forms\Set $set, $state) =>
                                    $state ? $set('jam', Carbon::now('Asia/Jakarta')->format('H:i:s')) : null
                                )
                                ->disabled(
                                    fn(): bool =>
                                    !(now('Asia/Jakarta')->hour >= 6 && now('Asia/Jakarta')->hour < 8)
                                ),

                            Forms\Components\Toggle::make('is_siang')
                                ->label('Siang (11:00 - 13:00)')
                                ->live()
                                ->afterStateUpdated(
                                    fn(Forms\Set $set, $state) =>
                                    $state ? $set('jam', Carbon::now('Asia/Jakarta')->format('H:i:s')) : null
                                )
                                ->disabled(
                                    fn(): bool =>
                                    !(now('Asia/Jakarta')->hour >= 11 && now('Asia/Jakarta')->hour < 13)
                                ),

                            Forms\Components\Toggle::make('is_sore')
                                ->label('Sore (16:00 - 18:00)')
                                ->live()
                                ->afterStateUpdated(
                                    fn(Forms\Set $set, $state) =>
                                    $state ? $set('jam', Carbon::now('Asia/Jakarta')->format('H:i:s')) : null
                                )
                                ->disabled(
                                    fn(): bool =>
                                    !(now('Asia/Jakarta')->hour >= 16 && now('Asia/Jakarta')->hour < 18)
                                ),

                            Forms\Components\Toggle::make('is_malam')
                                ->label('Malam (19:00 - 22:00)')
                                ->live()
                                ->afterStateUpdated(
                                    fn(Forms\Set $set, $state) =>
                                    $state ? $set('jam', Carbon::now('Asia/Jakarta')->format('H:i:s')) : null
                                )
                                ->disabled(
                                    fn(): bool =>
                                    !(now('Asia/Jakarta')->hour >= 19 && now('Asia/Jakarta')->hour < 24)
                                ),
                        ])->columns(2),
                        Forms\Components\TextInput::make('jam')
                            ->label('Jam Saat Ini')
                            ->disabled()
                            ->dehydrated()
                            ->default(null),
                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->columnSpanFull()
                            ->placeholder('Masukkan keterangan tambahan terkait waktu pemberian.')
                            ->rows(3),
                    ])->columns(2),
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
                Tables\Columns\IconColumn::make('is_pagi')
                    ->label('Pagi')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_siang')
                    ->label('Siang')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_sore')
                    ->label('Sore')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_malam')
                    ->label('Malam')
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
                TernaryFilter::make('is_pagi')
                    ->label('Filter Waktu Pagi'),
                TernaryFilter::make('is_siang')
                    ->label('Filter Waktu Siang'),
                TernaryFilter::make('is_sore')
                    ->label('Filter Waktu Sore'),
                TernaryFilter::make('is_malam')
                    ->label('Filter Waktu Malam'),
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
            'index' => Pages\ListBenarWaktus::route('/'),
            'create' => Pages\CreateBenarWaktu::route('/create'),
            'edit' => Pages\EditBenarWaktu::route('/{record}/edit'),
        ];
    }
}
