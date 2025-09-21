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
use Illuminate\Support\Facades\Auth;
use App\Filament\Widgets\BenarCaraStats;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Widgets\BenarHakClientStats;
use App\Filament\Resources\BenarCaraResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BenarCaraResource\RelationManagers;
use App\Filament\Widgets\PersentaseKepatuhan\BenarCaraPercentageStats;

class BenarCaraResource extends Resource
{
    protected static ?string $model = BenarCara::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';
    protected static ?string $pluralModelLabel = 'Benar Cara Pemberian';
    protected static ?string $modelLabel = 'Benar Cara Pemberian';
    protected static ?string $navigationGroup = 'Hasil Pemeriksaan';
    protected static ?int $navigationSort = 4;


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
                        Forms\Components\Fieldset::make('Verifikasi Cara Pemberian')
                            ->schema([
                                Forms\Components\Toggle::make('is_oral')
                                    ->label('Diberikan secara oral (melalui mulut)'),
                                Forms\Components\Toggle::make('is_iv')
                                    ->label('Diberikan melalui injeksi ke pembuluh vena (secara intravena, IV)'),
                                Forms\Components\Toggle::make('is_im')
                                    ->label('Pemberian secara intramuskular, IM)'),
                                Forms\Components\Toggle::make('is_intratekal')
                                    ->label('Pemberian via sumsum tulang belakang (secara intratekal)'),
                                Forms\Components\Toggle::make('is_subkutan')
                                    ->label('Pemberian di bawah kulit (secara subkutan)'),
                                Forms\Components\Toggle::make('is_sublingual')
                                    ->label('Pemberian di bawah lidah (secara sublingual)'),
                                Forms\Components\Toggle::make('is_rektal')
                                    ->label('Pemberian dimasukkan ke dalam anus (secara rektal)'),
                                Forms\Components\Toggle::make('is_vaginal')
                                    ->label('Pemberian dimasukkan ke dalam vagina (secara vaginal)'),
                                Forms\Components\Toggle::make('is_okular')
                                    ->label('Pemberian di mata (melalui rute okular)'),
                                Forms\Components\Toggle::make('is_otik')
                                    ->label('Pemberian di telinga (melalui rute otik)'),
                                Forms\Components\Toggle::make('is_nasal')
                                    ->label('Disemprotkan ke dalam hidung dan diserap melalui membran hidung (secara nasal)'),
                                Forms\Components\Toggle::make('is_nebulisasi')
                                    ->label('Dihirup ke paru-paru, secara nebulisasi'),
                                Forms\Components\Toggle::make('is_topikal')
                                    ->label('Diaplikasikan pada kulit untuk efek lokal (topikal)'),
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
                Tables\Columns\IconColumn::make('is_oral')->label('Oral')->boolean(),
                Tables\Columns\IconColumn::make('is_iv')->label('Intravena')->boolean(),
                Tables\Columns\IconColumn::make('is_im')->label('Intramuskular')->boolean(),
                Tables\Columns\IconColumn::make('is_intratekal')->label('Intratekal')->boolean()->toggleable(),
                Tables\Columns\IconColumn::make('is_subkutan')->label('Subkutan')->boolean()->toggleable(),
                Tables\Columns\IconColumn::make('is_sublingual')->label('Sublingual')->boolean()->toggleable(),
                Tables\Columns\IconColumn::make('is_rektal')->label('Rektal')->boolean()->toggleable(),
                Tables\Columns\IconColumn::make('is_vaginal')->label('Vaginal')->boolean()->toggleable(),
                Tables\Columns\IconColumn::make('is_okular')->label('Okular')->boolean()->toggleable(),
                Tables\Columns\IconColumn::make('is_otik')->label('Otik')->boolean()->toggleable(),
                Tables\Columns\IconColumn::make('is_nasal')->label('Nasal')->boolean()->toggleable(),
                Tables\Columns\IconColumn::make('is_nebulisasi')->label('Nebulisasi')->boolean()->toggleable(),
                Tables\Columns\IconColumn::make('is_topikal')->label('Topikal')->boolean()->toggleable(),
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
                Tables\Filters\TernaryFilter::make('is_oral')->label('Filter Oral'),
                Tables\Filters\TernaryFilter::make('is_iv')->label('Filter Intravena'),
                Tables\Filters\TernaryFilter::make('is_im')->label('Filter Intramuskular'),
                Tables\Filters\TernaryFilter::make('is_intratekal')->label('Filter Intratekal'),
                Tables\Filters\TernaryFilter::make('is_subkutan')->label('Filter Subkutan'),
                Tables\Filters\TernaryFilter::make('is_sublingual')->label('Filter Sublingual'),
                Tables\Filters\TernaryFilter::make('is_rektal')->label('Filter Rektal'),
                Tables\Filters\TernaryFilter::make('is_vaginal')->label('Filter Vaginal'),
                Tables\Filters\TernaryFilter::make('is_okular')->label('Filter Okular'),
                Tables\Filters\TernaryFilter::make('is_otik')->label('Filter Otik'),
                Tables\Filters\TernaryFilter::make('is_nasal')->label('Filter Nasal'),
                Tables\Filters\TernaryFilter::make('is_nebulisasi')->label('Filter Nebulisasi'),
                Tables\Filters\TernaryFilter::make('is_topikal')->label('Filter Topikal'),
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
