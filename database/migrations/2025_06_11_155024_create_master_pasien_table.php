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
        Schema::create('master_pasien', function (Blueprint $table) {
            $table->increments('nourut'); // Primary Key dari 'nourut'
            $table->string('iol', 1)->nullable();
            $table->dateTime('tgl_kunj')->nullable();
            $table->string('no_cm', 8)->unique()->nullable(); // Unique key untuk Nomor Rekam Medis
            $table->string('nama_pas', 40)->nullable();
            $table->dateTime('tgl_lahir')->nullable();
            $table->string('alamat', 200)->nullable();
            $table->string('kec', 10)->nullable();
            $table->string('nama_ortu', 30)->nullable();
            $table->string('pek_pasien', 30)->nullable();
            $table->string('pek_ortu', 30)->nullable();
            $table->string('alm_ortu', 200)->nullable();
            $table->char('sex', 1)->nullable(); // Diubah dari varchar(10) ke char(1) untuk efisiensi (P/L)
            $table->char('kelas', 5)->nullable();
            $table->string('no_reg', 8)->nullable(); // Nomor Registrasi (kunjungan)
            $table->string('unit', 20)->nullable();
            $table->string('pend', 20)->nullable();
            $table->string('agama', 10)->nullable();
            $table->time('jam')->nullable(); // Diubah dari varchar(8) ke time
            $table->string('diagnosa', 50)->nullable();
            $table->string('bl', 1)->nullable();
            $table->string('identitas', 30)->nullable();
            $table->string('pengirim', 40)->nullable();
            $table->string('gol', 2)->nullable();
            $table->string('gawat', 10)->nullable();
            $table->string('al_kir', 45)->nullable();
            $table->string('nama_peng', 45)->nullable();
            $table->string('pin', 1)->nullable();
            $table->dateTime('tgl_kunj1')->nullable();
            $table->dateTime('tgl_kunj2')->nullable();
            $table->string('desa', 15)->default('-');
            $table->time('jam1')->nullable(); // Diubah dari varchar(8) ke time
            $table->time('jam2')->nullable(); // Diubah dari varchar(8) ke time
            $table->string('kls1', 1)->nullable();
            $table->string('kls2', 1)->nullable();
            $table->string('kkk', 2)->nullable();
            $table->string('kdu', 2)->nullable();
            $table->string('status', 30)->nullable();
            $table->string('hub', 15)->nullable();
            $table->string('kab', 10)->nullable();
            $table->string('telp', 20)->nullable();
            $table->string('asuransi', 40)->nullable();
            $table->string('aktif', 6)->nullable();
            $table->integer('no_px')->nullable();
            $table->string('petugas_tpp', 40)->nullable();
            $table->string('dokter', 40)->nullable();
            $table->string('perawat', 40)->nullable();
            $table->dateTime('tgl_pl')->nullable();
            $table->time('jam_pl')->nullable(); // Diubah dari varchar(8) ke time
            $table->string('bbbb', 10)->nullable();
            $table->string('kd_dx', 6)->nullable();
            $table->string('waktu', 6)->nullable();
            $table->string('telp_ortu', 20)->nullable();
            $table->integer('kunjungan')->nullable();
            $table->string('cara_masuk', 20)->nullable();
            $table->string('polisi', 5)->nullable();
            $table->integer('ruang')->nullable();
            $table->dateTime('tgl_inap')->nullable();
            $table->char('jam_inap', 8)->nullable(); // Biarkan char(8) jika format bisa bervariasi 'HH:MM' atau 'HH:MM:SS'
            $table->string('status_kary', 30)->nullable();
            $table->char('gzresp', 1)->default('T');
            $table->integer('cek')->nullable();
            $table->integer('id_menikah')->nullable();
            $table->char('id_propinsi', 2)->nullable();
            $table->char('no_kpsta', 30)->nullable();
            $table->char('asal_daerah', 40)->nullable();
            $table->integer('id_retensi')->nullable();
            $table->integer('id_alergi')->nullable();
            $table->integer('fis_asuhan')->nullable();
            $table->string('catatan_bpjs', 200)->nullable(); // Diubah dari char ke varchar
            $table->boolean('cek_kpsta')->default(0); // Diubah dari int ke boolean
            $table->boolean('cek_ktp')->default(0); // Diubah dari int ke boolean
            $table->boolean('cek_kk')->default(0); // Diubah dari int ke boolean
            $table->boolean('bank_v_lab')->default(0); // Diubah dari int ke boolean
            $table->boolean('bank_v_far')->default(0); // Diubah dari int ke boolean
            $table->boolean('bank_v_rad')->default(0); // Diubah dari int ke boolean
            $table->boolean('bank_v_gz')->default(0); // Diubah dari int ke boolean
            $table->boolean('bank_v_fis')->default(0); // Diubah dari int ke boolean
            $table->char('nik', 20)->nullable();
            $table->boolean('flag_mcu')->default(0); // Diubah dari int ke boolean
            $table->boolean('flag_penyakit')->default(0); // Diubah dari int ke boolean
            $table->boolean('flag_pasien')->default(0); // Diubah dari int ke boolean
            $table->integer('flag_status')->nullable();
            $table->boolean('flag_prolanis')->default(0); // Diubah dari int ke boolean
            $table->text('paraf')->nullable();
            $table->char('id_prop_domisili', 10)->nullable();
            $table->char('id_kab_domisili', 10)->nullable();
            $table->char('id_kec_domisili', 10)->nullable();
            $table->char('id_desa_domisili', 10)->nullable();
            $table->string('alamat_domisili', 200)->nullable(); // Diubah dari char ke varchar
            $table->string('lokasi_domisili', 250)->nullable(); // Diubah dari char ke varchar
            $table->char('ihs', 100)->nullable();
            $table->char('reg_sitb', 100)->nullable();
            $table->char('no_sep', 20)->nullable();
            $table->char('ihs_labmu', 50)->nullable();
            $table->boolean('is_hiv')->default(0); // Diubah dari int ke boolean
            $table->boolean('is_hbs')->default(0); // Diubah dari int ke boolean
            $table->boolean('is_tbc')->default(0); // Diubah dari int ke boolean
            $table->boolean('is_pulang')->default(0); // Diubah dari int ke boolean
            $table->boolean('is_titip')->default(0); // Diubah dari int ke boolean
            $table->boolean('is_farmasi')->default(0); // Diubah dari int ke boolean
            $table->boolean('is_radiologi')->default(0); // Diubah dari int ke boolean
            $table->boolean('is_laboratorium')->default(0); // Diubah dari int ke boolean
            $table->char('tempat', 50)->nullable();
            $table->timestamps();

            // Indexes dari SQL asli
            $table->index('nama_pas');
            $table->index('alamat');
            $table->index('no_reg');
            $table->index('iol');
            $table->index('tgl_kunj');
            $table->index('dokter');
            $table->index('unit');
            $table->index('asuransi');
            $table->index('aktif');
            $table->index('tgl_inap');
            $table->index('flag_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_pasien');
    }
};
