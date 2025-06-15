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
        Schema::create('faktur', function (Blueprint $table) {
            $table->id();
            $table->string('npwp_penjual', 30)->nullable();
            $table->integer('baris')->nullable();
            $table->date('tanggal_faktur')->nullable();
            $table->string('jenis_faktur', 50)->nullable();
            $table->string('kode_transaksi', 10)->nullable();
            $table->text('keterangan_tambahan')->nullable();
            $table->text('dokumen_pendukung')->nullable();
            $table->string('referensi', 100)->nullable();
            $table->string('cap_fasilitas', 100)->nullable();
            $table->string('id_tku_penjual', 30)->nullable();
            $table->string('npwp_nik_pembeli', 30)->nullable();
            $table->string('jenis_id_pembeli', 10)->nullable();
            $table->string('negara_pembeli', 10)->nullable();
            $table->string('nomor_dokumen_pembeli', 100)->nullable();
            $table->string('nama_pembeli', 255)->nullable();
            $table->text('alamat_pembeli')->nullable();
            $table->string('email_pembeli', 255)->nullable();
            $table->string('id_tku_pembeli', 30)->nullable();
            $table->boolean('uang_muka')->default(false);
            $table->boolean('pelunasan')->default(false);
            $table->text('alamat_penjual')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faktur');
    }
};