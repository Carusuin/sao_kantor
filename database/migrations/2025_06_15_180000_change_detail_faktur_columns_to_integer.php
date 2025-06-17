<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_faktur', function (Blueprint $table) {
            $table->integer('harga_satuan')->change();
            $table->integer('jumlah_barang_jasa')->change();
            $table->integer('total_diskon')->change();
            $table->integer('dpp')->change();
            $table->integer('dpp_nilai_lain')->change();
            $table->integer('tarif_ppn')->change();
            $table->integer('ppn')->change();
            $table->integer('tarif_ppnbm')->change();
            $table->integer('ppnbm')->change();
        });
    }

    public function down(): void
    {
        Schema::table('detail_faktur', function (Blueprint $table) {
            $table->decimal('harga_satuan', 15, 2)->change();
            $table->decimal('jumlah_barang_jasa', 15, 2)->change();
            $table->decimal('total_diskon', 15, 2)->change();
            $table->decimal('dpp', 15, 2)->change();
            $table->decimal('dpp_nilai_lain', 15, 2)->change();
            $table->decimal('tarif_ppn', 5, 2)->change();
            $table->decimal('ppn', 15, 2)->change();
            $table->decimal('tarif_ppnbm', 5, 2)->change();
            $table->decimal('ppnbm', 15, 2)->change();
        });
    }
}; 