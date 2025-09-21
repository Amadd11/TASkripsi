<?php

namespace App\Filament\Pages;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Models\Pemeriksaan;
use App\Models\MasterPasien;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use App\Filament\Resources\PemeriksaanResource;

class PascaTindakan extends Page implements HasForms
{
    use HasPageShield;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';
    protected static string $view = 'filament.pages.pasca-tindakan';
    protected static ?string $title = 'Verifikasi Pasca Tindakan';
    protected static ?string $navigationGroup = 'Pemeriksaan';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'pasca-tindakan/{record}';

    public ?array $data = [];
    public ?Pemeriksaan $record = null;

    public function mount(Pemeriksaan $record): void
    {
        $this->record = $record;
        $this->data = $this->record->attributesToArray();
        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pasien')
                    ->description('Data pasien dari pemeriksaan yang sudah ada.')
                    ->schema([
                        Forms\Components\TextInput::make('no_cm')
                            ->label('Nomor Rekam Medis (No. CM)')
                            ->disabled(),
                        Forms\Components\TextInput::make('no_reg')
                            ->label('Nomor Registrasi')
                            ->disabled(),
                        Forms\Components\TextInput::make('nama_pas')
                            ->label('Nama Pasien')
                            ->disabled(),
                        Forms\Components\TextInput::make('tgl_lahir')
                            ->label('Tanggal Lahir')
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('Verifikasi Pasca Tindakan')
                    ->schema([
                        Forms\Components\Section::make('6. Benar Dokumentasi')
                            ->icon('heroicon-o-document-text')->collapsible()->schema([
                                Forms\Components\Toggle::make('dok_is_pasien')->label('Dokumentasi Pasien Benar?')->required(),
                                Forms\Components\Toggle::make('dok_is_dosis')->label('Dokumentasi Dosis Benar?')->required(),
                                Forms\Components\Toggle::make('dok_is_obat')->label('Dokumentasi Obat Benar?')->required(),
                                Forms\Components\Toggle::make('dok_is_waktu')->label('Dokumentasi Waktu Benar?')->required(),
                                Forms\Components\Toggle::make('dok_is_rute')->label('Dokumentasi Rute Benar?')->required(),
                                Forms\Components\Textarea::make('dok_keterangan')->label('Keterangan')->rows(2),
                            ]),
                        Forms\Components\Section::make('7. Benar Evaluasi')
                            ->icon('heroicon-o-check-circle')->collapsible()->collapsed()->schema([
                                Forms\Components\Toggle::make('evaluasi_is_efek_samping')->label('Ada Efek Samping?')->required(),
                                Forms\Components\Toggle::make('evaluasi_is_alergi')->label('Ada Reaksi Alergi?')->required(),
                                Forms\Components\Toggle::make('evaluasi_is_efek_terapi')->label('Efek Terapi tercapai?')->required(),
                                Forms\Components\Textarea::make('evaluasi_keterangan')->label('Keterangan')->rows(2),
                            ]),
                        Forms\Components\Section::make('9. Benar Reaksi Obat')
                            ->icon('heroicon-o-exclamation-triangle')->collapsible()->collapsed()->schema([
                                Forms\Components\Toggle::make('reaksi_obat_is_efek_samping')->label('Ada Efek Samping?')->required(),
                                Forms\Components\Toggle::make('reaksi_obat_is_alergi')->label('Ada Reaksi Alergi?')->required(),
                                Forms\Components\Toggle::make('reaksi_obat_is_efek_terapi')->label('Efek Terapi tercapai?')->required(),
                                Forms\Components\Textarea::make('reaksi_obat_keterangan')->label('Keterangan')->rows(2),
                            ]),
                        Forms\Components\Section::make('10. Benar Reaksi Makanan')
                            ->icon('heroicon-o-cake')->collapsible()->collapsed()->schema([
                                Forms\Components\Toggle::make('reaksi_makanan_is_efek_makanan')->label('Apakah ada Efek Reaksi Makanan?')->required(),
                                Forms\Components\Textarea::make('reaksi_makanan_keterangan')->label('Keterangan')->rows(2),
                            ]),
                        Forms\Components\Section::make('12. Benar Pendidikan')
                            ->icon('heroicon-o-academic-cap')->collapsible()->collapsed()->schema([
                                Forms\Components\Toggle::make('pendidikan_is_edukasi')->label('Apakah Edukasi sudah diberikan?')->required(),
                                Forms\Components\Textarea::make('pendidikan_keterangan')->label('Keterangan')->rows(2),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $formData = $this->form->getState();
        DB::transaction(function () use ($formData) {
            $this->record->update(array_merge($formData, [
                'status' => 'Selesai',
                'updated_at' => now(),
            ]));
        });

        Notification::make()
            ->title('Checklist Pasca Tindakan Berhasil Diperbarui')
            ->success()
            ->send();

        $this->redirect(PemeriksaanResource::getUrl('index'));
    }
}
