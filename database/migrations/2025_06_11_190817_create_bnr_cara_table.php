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
        Schema::create('bnr_cara', function (Blueprint $table) {
            $table->increments('id_bnr_cara');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->boolean('is_oral')->default(0); // Diubah dari int ke boolean
            $table->boolean('is_iv')->default(0); // Diubah dari int ke boolean
            $table->integer('is_no_reg')->nullable()->default(0); // Tetap int karena defaultnya 0
            $table->date('tanggal')->nullable();
            $table->time('jam')->nullable(); // Diubah dari char(8) ke time
            $table->text('keterangan')->nullable();
            $table->char('no_reg', 8)->nullable();
            $table->char('no_cm', 8)->nullable();
            $table->boolean('is_im')->default(0); // Diubah dari int ke boolean
            $table->timestamps();
            // Foreign Keys
            $table->foreign('no_cm')->references('no_cm')->on('master_pasien')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bnr_cara');
    }
};
