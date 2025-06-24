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
        Schema::create('far_transactions', function (Blueprint $table) {
            $table->increments('no_trn'); // Primary Key
            $table->string('no_cm', 8)->nullable();
            $table->dateTime('tgl')->nullable();
            $table->time('jam')->nullable(); // Diubah dari varchar(10) ke time
            $table->string('unit', 15)->nullable();
            $table->string('dokter', 30)->nullable();
            $table->string('petugas', 30)->nullable();
            $table->string('sampel', 30)->nullable();
            $table->string('no_reg', 8)->nullable();
            $table->string('pengirim', 30)->nullable();
            $table->integer('biaya')->nullable();
            $table->string('cetak', 5)->nullable();
            $table->string('lunas', 5)->default('BELUM');
            $table->dateTime('tgl_lahir')->nullable();
            $table->string('alamat', 50)->nullable();
            $table->char('sex', 1)->nullable();
            $table->char('kelas', 5)->nullable();
            $table->string('nama_pas', 30)->nullable();
            $table->string('iol', 1)->nullable();
            $table->string('rujuk', 20)->nullable();
            $table->string('bl_kunj', 1)->nullable();
            $table->char('shift', 1)->nullable();
            $table->integer('no_psn')->nullable();
            $table->char('asuransi', 30)->nullable();
            $table->integer('biaya_pns')->nullable();
            $table->date('tgl_ambil', 10)->nullable();
            $table->time('jam_ambil', 5)->nullable();
            $table->integer('menit')->nullable();
            $table->integer('panggil')->nullable();
            $table->integer('racikan')->nullable();
            $table->integer('bpjs')->nullable();
            $table->string('catatan', 200)->nullable(); // Diubah dari char ke varchar
            $table->integer('loket')->nullable();
            $table->integer('sub_embalase')->nullable();
            $table->integer('sub_er')->nullable();
            $table->integer('sub_racikan')->nullable();
            $table->integer('sub_item_er')->nullable();
            $table->integer('grand_total')->nullable();
            $table->integer('bayar')->nullable();
            $table->integer('emr')->nullable();
            $table->integer('bagian')->default(0);
            $table->integer('gp')->default(0);
            $table->integer('id_h_cp')->default(0);
            $table->integer('klinik_online')->default(0);
            $table->integer('f_terapi_plg')->default(0);
            $table->integer('no_tunggu')->nullable();
            $table->boolean('vlag_saji')->default(0); // Diubah dari int ke boolean
            $table->date('tanggal_saji')->nullable();
            $table->char('jam_saji', 8)->nullable();
            $table->boolean('tr_Jelas')->default(0); // Diubah dari int ke boolean
            $table->boolean('tr_obat')->default(0); // Diubah dari int ke boolean
            $table->boolean('tr_dosis')->default(0); // Diubah dari int ke boolean
            $table->boolean('tr_rute')->default(0); // Diubah dari int ke boolean
            $table->boolean('tr_waktu')->default(0); // Diubah dari int ke boolean
            $table->boolean('tr_duplikasi')->default(0); // Diubah dari int ke boolean
            $table->boolean('tr_interaksi')->default(0); // Diubah dari int ke boolean
            $table->boolean('tr_kontradiksi')->default(0); // Diubah dari int ke boolean
            $table->boolean('to_identitas')->default(0); // Diubah dari int ke boolean
            $table->boolean('to_obat')->default(0); // Diubah dari int ke boolean
            $table->boolean('to_jumlah')->default(0); // Diubah dari int ke boolean
            $table->boolean('to_waktu')->default(0); // Diubah dari int ke boolean
            $table->boolean('to_rute')->default(0); // Diubah dari int ke boolean
            $table->string('tr_lanjut', 250)->nullable(); // Diubah dari char ke varchar
            $table->string('to_lanjut', 250)->nullable(); // Diubah dari char ke varchar
            $table->integer('tr_petugas')->nullable();
            $table->integer('to_petugas')->nullable();
            $table->boolean('is_gofar')->default(0); // Diubah dari int ke boolean
            $table->boolean('is_dt')->default(0); // Diubah dari int ke boolean
            $table->boolean('is_onl')->default(0); // Diubah dari int ke boolean
            $table->boolean('is_kronis')->default(0); // Diubah dari int ke boolean
            $table->timestamps();
            // Foreign Keys (jika ada tabel master yang sesuai)
            // Asumsi no_cm merujuk ke master_pasien.no_cm
            // Pastikan 'master_pasien' dibuat terlebih dahulu.
            $table->foreign('no_cm')->references('no_cm')->on('master_pasien')->onDelete('set null');

            // Indexes dari SQL asli
            $table->index('no_reg');
            $table->index('panggil');
            $table->index('asuransi');
            $table->index('unit');
            $table->index('no_cm');
            $table->index('no_psn');
            $table->index('iol');
            $table->index('tgl');
            $table->index('dokter');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('far_transactions');
    }
};
