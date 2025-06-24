<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\BenarDosis;
use Filament\Tables\Table;
use App\Models\MasterPasien;
use Filament\Resources\Resource;
use Filament\Forms\FormsComponent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BenarDosisResource\Pages;
use App\Filament\Resources\BenarDosisResource\RelationManagers;

class BenarDosisResource extends Resource
{
    protected static ?string $model = BenarDosis::class;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-vertical';
    protected static ?string $navigationGroup = 'Prinsip 12 Benar';
    protected static ?string $pluralModelLabel = 'Benar Dosis';
    protected static ?string $modelLabel = 'Benar Dosis';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Benar Dosis')
                    ->description('Pastikan semua data terkait Dosis sudah benar.')
                    ->schema([
                        Forms\Components\Select::make('no_cm')
                            ->label('Nomor Rekam Medis Pasien')
                            ->relationship(
                                'masterPasien',
                                'no_cm',
                                function ($query) {
                                    $query->whereNotIn('no_cm', function ($subquery) {
                                        $subquery->select('no_cm')->from('bnr_dosis');
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
                            ->label('Nomor Registrasi ')
                            ->disabled(),
                        Forms\Components\Fieldset::make('Verifikasi Dosis & Potensi')
                            ->schema([
                                Forms\Components\Toggle::make('is_jumlah')
                                    ->label('Apakah Jumlah/Dosis sudah sesuai?')
                                    ->hint('Centang jika jumlah atau dosis yang diberikan sudah benar.')
                                    ->required(),
                                Forms\Components\Toggle::make('is_potensi')
                                    ->label('Apakah Potensi/Kekuatan sudah sesuai?')
                                    ->hint('Centang jika potensi atau kekuatan obat sudah sesuai.')
                                    ->required(),
                            ])->columns(1),
                        Forms\Components\DatePicker::make('tanggal')
                            ->label('Tanggal')
                            ->required(),
                        Forms\Components\TimePicker::make('jam')
                            ->label('Jam')
                            ->required(),
                        Forms\Components\TextInput::make('id_petugas')
                            ->label('ID Petugas')
                            ->numeric(),
                        Forms\Components\TextInput::make('is_no_reg')
                            ->label('Nomor Registrasi Internal')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('Nomor registrasi internal untuk pencatatan.'),
                        Forms\Components\Textarea::make('keterangan')
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
                Tables\Columns\IconColumn::make('is_jumlah')
                    ->label('Jumlah Benar')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_potensi')
                    ->label('Potensi Benar')
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
                //
                Tables\Filters\TernaryFilter::make('is_jumlah')
                    ->label('Filter Jumlah Benar'),
                Tables\Filters\TernaryFilter::make('is_potensi')
                    ->label('Filter Potensi Benar'),
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
            'index' => Pages\ListBenarDoses::route('/'),
            'create' => Pages\CreateBenarDosis::route('/create'),
            'edit' => Pages\EditBenarDosis::route('/{record}/edit'),
        ];
    }
}
