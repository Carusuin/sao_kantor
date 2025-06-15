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
        Schema::table('detail_faktur', function (Blueprint $table) {
            $table->foreign('faktur_id', 'fk_detail_faktur')->references('id')->on('faktur')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_faktur', function (Blueprint $table) {
            $table->dropForeign('fk_detail_faktur');
        });
    }
};