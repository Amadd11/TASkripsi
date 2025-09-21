<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Pemeriksaan;
use Filament\Resources\Resource;
use App\Filament\Pages\PraTindakan;
use Filament\Tables\Actions\Action;
use App\Filament\Pages\PascaTindakan;
use App\Filament\Pages\PraTindakanPage;
use App\Filament\Pages\PascaTindakanPage;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PemeriksaanResource\Pages;

class PemeriksaanResource extends Resource
{
    protected static ?string $model = Pemeriksaan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Pemeriksaan';
    protected static ?string $navigationLabel = 'Hasil Pemeriksaan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('no_cm')->label('Nomor Rekam Medis (No. CM)'),
                Forms\Components\TextInput::make('nama_pas')->label('Nama Pasien'),
                Forms\Components\TextInput::make('status')->label('Status'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_cm')->label('No. CM')->searchable(),
                Tables\Columns\TextColumn::make('no_reg')->label('No. Registrasi')->searchable(),
                Tables\Columns\TextColumn::make('nama_pas')->label('Nama Pasien')->searchable(),
                Tables\Columns\TextColumn::make('tanggal')->label('Tanggal')->date()->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Pra Tindakan' => 'warning',
                        'Selesai' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')->label('Dibuat')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->label('Diperbarui')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Filter Status')
                    ->options(['Pra Tindakan' => 'Pra Tindakan', 'Selesai' => 'Selesai']),
            ])
            ->actions([
                Action::make('Lanjutkan Pemeriksaan')
                    ->label('Lanjutkan Pemeriksaan')
                    ->icon('heroicon-o-pencil-square')
                    ->color('success')
                    ->url(fn(Pemeriksaan $record) => PascaTindakan::getUrl(['record' => $record->id]))
                    ->visible(fn(Pemeriksaan $record): bool => $record->status !== 'Selesai'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPemeriksaans::route('/'),
        ];
    }

    public static function getCreateUrl(): string
    {
        return PraTindakan::getUrl();
    }
}
