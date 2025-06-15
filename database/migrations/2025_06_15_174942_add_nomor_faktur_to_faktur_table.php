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
        Schema::table('faktur', function (Blueprint $table) {
            $table->string('nomor_faktur', 255)->nullable()->after('tanggal_faktur');
            $table->index('nomor_faktur');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faktur', function (Blueprint $table) {
            $table->dropIndex(['nomor_faktur']);
            $table->dropColumn('nomor_faktur');
        });
    }
};
