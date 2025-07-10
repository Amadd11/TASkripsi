<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('master_petugas', function (Blueprint $table) {
            $table->increments('no_urut');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // == Informasi Personal Utama ==
            $table->char('nik', 20)->nullable();
            $table->string('tempat_lahir', 30)->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->nullable();
            $table->enum('status_menikah', ['Sudah', 'Belum'])->nullable();
            $table->string('golongan_darah', 2)->nullable();
            $table->string('gelar_depan', 15)->nullable();
            $table->string('gelar_belakang', 30)->nullable();
            $table->char('initial', 2)->nullable();

            // == Informasi Kontak & Alamat ==
            $table->string('nama_kalurahan', 100)->nullable();
            $table->string('nama_kecamatan', 100)->nullable();
            $table->string('nama_propinsi', 100)->nullable();
            $table->string('alamat', 100)->nullable();
            $table->string('id_telegram', 100)->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->string('telepon', 20)->nullable();

            // == Informasi Kepegawaian ==
            $table->string('jabatan', 40)->nullable();
            $table->string('unit', 100)->nullable();
            $table->string('sip', 30)->nullable();
            $table->string('status_karyawan', 20)->nullable();

            // == Informasi Finansial ==
            $table->string('nomor_rekening', 30)->nullable();
            $table->string('nama_rekening', 50)->nullable();
            $table->string('npwp', 50)->nullable();

            // == Status & Lain-lain ==
            $table->boolean('biodata_aktif')->default(true);
            $table->string('paraf')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_petugas');
    }
};
