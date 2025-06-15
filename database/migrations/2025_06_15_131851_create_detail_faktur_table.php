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
        Schema::create('detail_faktur', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('faktur_id');
            $table->integer('baris');
            $table->char('barang_jasa', 1);
            $table->string('kode_barang_jasa', 50)->nullable();
            $table->string('nama_barang_jasa', 255);
            $table->string('nama_satuan_ukur', 50)->nullable();
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('jumlah_barang_jasa', 15, 2);
            $table->decimal('total_diskon', 15, 2)->default(0.00);
            $table->decimal('dpp', 15, 2);
            $table->decimal('dpp_nilai_lain', 15, 2)->default(0.00);
            $table->decimal('tarif_ppn', 5, 2)->default(11.00);
            $table->decimal('ppn', 15, 2);
            $table->decimal('tarif_ppnbm', 5, 2)->default(0.00);
            $table->decimal('ppnbm', 15, 2)->default(0.00);
            $table->timestamps();
            
            // Indexes
            $table->index('faktur_id');
            $table->index('baris', 'idx_baris');
            $table->index('barang_jasa', 'idx_barang_jasa');
            $table->index('kode_barang_jasa', 'idx_kode_barang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_faktur');
    }
};