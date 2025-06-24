<?php

namespace App\Filament\Resources;

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
use App\Filament\Resources\BenarWaktuResource\RelationManagers;

class BenarWaktuResource extends Resource
{
    protected static ?string $model = BenarWaktu::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Prinsip 12 Benar';

    protected static ?string $pluralModelLabel = 'Benar Waktu';

    protected static ?string $modelLabel = 'Benar Waktu';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Benar Waktu')
                    ->description('Pastikan semua data terkait waktu sudah benar.')
                    ->schema([
                        Forms\Components\Select::make('no_cm')
                            ->label('Nomor Rekam Medis Pasien')
                            ->relationship(
                                'masterPasien',
                                'no_cm',
                                function ($query) {
                                    $query->whereNotIn('no_cm', function ($subquery) {
                                        $subquery->select('no_cm')->from('bnr_waktu');
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
                        Forms\Components\TextInput::make('is_no_reg')
                            ->label('Nomor Registrasi Internal')
                            ->numeric()
                            ->default(0),
                        Forms\Components\DatePicker::make('tanggal')
                            ->label('Tanggal'), // Label form dalam Bahasa Indonesia
                        Forms\Components\TimePicker::make('jam')
                            ->label('Jam'), // Label form dalam Bahasa Indonesia
                        Forms\Components\TextInput::make('id_petugas')
                            ->label('ID Petugas') // Label form dalam Bahasa Indonesia
                            ->numeric(),
                        Forms\Components\Toggle::make('is_pagi')
                            ->label('Apakah Pagi?')
                            ->required(),
                        Forms\Components\Toggle::make('is_siang')
                            ->label('Apakah Siang?')
                            ->required(),
                        Forms\Components\Toggle::make('is_sore')
                            ->label('Apakah Sore?')
                            ->required(),
                        Forms\Components\Toggle::make('is_malam')
                            ->label('Apakah Malam?')
                            ->required(),
                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan') // Label form dalam Bahasa Indonesia
                            ->columnSpanFull(),
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
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\IconColumn::make('is_siang')
                    ->label('Siang')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\IconColumn::make('is_sore')
                    ->label('Sore')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\IconColumn::make('is_malam')
                    ->label('Malam')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('is_no_reg')
                    ->label('No. Reg Internal')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false), // Default disembunyikan di tabel
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y') // Format tanggal lebih mudah dibaca
                    ->sortable(),
                Tables\Columns\TextColumn::make('jam')
                    ->label('Jam')
                    ->time('H:i') // Format jam lebih mudah dibaca
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
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
