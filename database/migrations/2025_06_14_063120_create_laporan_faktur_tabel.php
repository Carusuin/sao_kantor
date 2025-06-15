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
        Schema::create('barang_jasa', function (Blueprint $table) {
            $table->id();
            $table->integer('baris')->comment('Nomor baris item');
            $table->string('jenis_barang_jasa', 1)->comment('B = Barang, J = Jasa');
            $table->string('kode_barang_jasa', 50)->comment('Kode barang/jasa');
            $table->string('nama_barang_jasa')->comment('Nama barang/jasa');
            $table->string('nama_satuan_ukur', 50)->comment('Satuan ukuran (Unit, Kg, dll)');
            $table->decimal('harga_satuan', 15, 2)->comment('Harga per satuan');
            $table->decimal('jumlah_barang_jasa', 10, 2)->comment('Jumlah barang/jasa');
            $table->decimal('total_diskon', 15, 2)->default(0)->comment('Total atau diskon');
            $table->decimal('dpp', 15, 2)->comment('Dasar Pengenaan Pajak');
            $table->decimal('dpp_nilai_lain', 15, 2)->default(0)->comment('DPP nilai lain');
            $table->decimal('tarif_ppn', 5, 2)->comment('Tarif PPN dalam persen');
            $table->decimal('ppn', 15, 2)->comment('Nilai PPN');
            $table->decimal('tarif_ppnbm', 5, 2)->default(0)->comment('Tarif PPnBM dalam persen');
            $table->decimal('ppnbm', 15, 2)->default(0)->comment('Nilai PPnBM');
            
            // Foreign key jika terkait dengan dokumen/invoice
            $table->unsignedBigInteger('dokumen_id')->nullable()->comment('ID dokumen terkait');
            
            $table->timestamps();
            
            // Index untuk optimasi query
            $table->index(['dokumen_id', 'baris']);
            $table->index('kode_barang_jasa');
            
            // Foreign key constraint (opsional, sesuaikan dengan tabel dokumen Anda)
            // $table->foreign('dokumen_id')->references('id')->on('dokumen')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_jasa');
    }
};