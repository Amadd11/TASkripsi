<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class TentangAplikasi extends Page
{
    /**
     * Tampilan Blade yang akan digunakan untuk halaman ini.
     */
    protected static string $view = 'filament.pages.tentang-aplikasi';

    /**
     * Ikon yang akan muncul di sidebar.
     * Anda bisa mencari ikon lain di https://heroicons.com/
     */
    protected static ?string $navigationIcon = 'heroicon-o-information-circle';

    /**
     * Label yang akan ditampilkan di menu navigasi sidebar.
     */
    protected static ?string $navigationLabel = 'Tentang Aplikasi';

    /**
     * (Opsional) Mengelompokkan menu ini di bawah grup tertentu.
     */
    protected static ?string $navigationGroup = 'Bantuan';

    /**
     * (Opsional) Mengatur urutan menu di dalam grup.
     * Angka yang lebih kecil akan berada di atas.
     */
    protected static ?int $navigationSort = 100;

    /**
     * Judul yang akan ditampilkan di dalam konten halaman.
     */
    protected ?string $heading = 'Tentang Aplikasi';

    /**
     * Sub-judul yang akan ditampilkan di bawah judul utama.
     */
    protected ?string $subheading = 'Informasi detail mengenai aplikasi 12 SMART-MU.';
}
