<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_faktur', function (Blueprint $table) {
            $table->integer('total_harga')->after('harga_satuan')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('detail_faktur', function (Blueprint $table) {
            $table->dropColumn('total_harga');
        });
    }
}; 