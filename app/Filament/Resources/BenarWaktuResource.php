<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\BenarWaktu;
use Filament\Tables\Table;
use App\Models\MasterPasien;
use Filament\Resources\Resource;
use Filament\Forms\Components\Fieldset; // Import Fieldset
use Filament\Tables\Filters\TernaryFilter; // Import TernaryFilter
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BenarWaktuResource\Pages;
use App\Filament\Resources\BenarWaktuResource\RelationManagers;

class BenarWaktuResource extends Resource
{
    protected static ?string $model = BenarWaktu::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $pluralModelLabel = 'Benar Waktu';
    protected static ?string $modelLabel = 'Benar Waktu';
    protected static ?string $navigationGroup = 'Hasil Pemeriksaan';

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
                        Fieldset::make('Verifikasi Waktu Pemberian')
                            ->schema([
                                Forms\Components\Toggle::make('is_pagi')
                                    ->label('Pagi')
                                    ->hint('Centang jika pemberian sesuai waktu pagi.')
                                    ->required(),
                                Forms\Components\Toggle::make('is_siang')
                                    ->label('Siang')
                                    ->hint('Centang jika pemberian sesuai waktu siang.')
                                    ->required(),
                                Forms\Components\Toggle::make('is_sore')
                                    ->label('Sore')
                                    ->hint('Centang jika pemberian sesuai waktu sore.')
                                    ->required(),
                                Forms\Components\Toggle::make('is_malam')
                                    ->label('Malam')
                                    ->hint('Centang jika pemberian sesuai waktu malam.')
                                    ->required(),
                            ])->columns(1),
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
                Tables\Columns\TextColumn::make('masterPasien.no_cm')
                    ->label('No. CM Pasien')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('masterPasien.no_reg')
                    ->label('No. Reg Transaksi')
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
