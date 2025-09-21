<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::create('pemeriksaans', function (Blueprint $table) {
            $table->id();

            // Data Umum Pemeriksaan
            $table->foreign('no_cm')->references('no_cm')->on('master_pasien')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('no_cm')->nullable();
            $table->string('no_reg')->nullable();
            $table->string('nama_pas')->nullable();
            $table->date('tanggal')->nullable();
            $table->time('jam')->nullable();
            $table->string('status')->default('Pra Tindakan'); // 'Pra Tindakan' atau 'Selesai'

            // Pra Tindakan
            $table->boolean('pasien_is_nama')->default(false);
            $table->boolean('pasien_is_tgl_lahir')->default(false);
            $table->text('pasien_keterangan')->nullable();

            $table->boolean('obat_is_nama_obat')->default(false);
            $table->boolean('obat_is_label')->default(false);
            $table->boolean('obat_is_resep')->default(false);
            $table->text('obat_keterangan')->nullable();

            $table->boolean('dosis_is_jumlah')->default(false);
            $table->boolean('dosis_is_potensi')->default(false);
            $table->text('dosis_keterangan')->nullable();

            $table->boolean('cara_is_oral')->default(false);
            $table->boolean('cara_is_iv')->default(false);
            $table->boolean('cara_is_im')->default(false);
            $table->text('cara_keterangan')->nullable();

            $table->boolean('waktu_is_pagi')->default(false);
            $table->boolean('waktu_is_siang')->default(false);
            $table->boolean('waktu_is_sore')->default(false);
            $table->boolean('waktu_is_malam')->default(false);
            $table->text('waktu_keterangan')->nullable();

            $table->boolean('pengkajian_is_suhu')->default(false);
            $table->boolean('pengkajian_is_tensi')->default(false);
            $table->text('pengkajian_keterangan')->nullable();

            $table->boolean('hak_is_ic')->default(false);
            $table->text('hak_keterangan')->nullable();

            // Pasca Tindakan
            $table->boolean('dok_is_pasien')->default(false);
            $table->boolean('dok_is_dosis')->default(false);
            $table->boolean('dok_is_obat')->default(false);
            $table->boolean('dok_is_waktu')->default(false);
            $table->boolean('dok_is_rute')->default(false);
            $table->text('dok_keterangan')->nullable();

            $table->boolean('evaluasi_is_efek_samping')->default(false);
            $table->boolean('evaluasi_is_alergi')->default(false);
            $table->boolean('evaluasi_is_efek_terapi')->default(false);
            $table->text('evaluasi_keterangan')->nullable();

            $table->boolean('reaksi_obat_is_efek_samping')->default(false);
            $table->boolean('reaksi_obat_is_alergi')->default(false);
            $table->boolean('reaksi_obat_is_efek_terapi')->default(false);
            $table->text('reaksi_obat_keterangan')->nullable();

            $table->boolean('reaksi_makanan_is_efek_makanan')->default(false);
            $table->text('reaksi_makanan_keterangan')->nullable();

            $table->boolean('pendidikan_is_edukasi')->default(false);
            $table->text('pendidikan_keterangan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Mundurkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemeriksaans');
    }
};
