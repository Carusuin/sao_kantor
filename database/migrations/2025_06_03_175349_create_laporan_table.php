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
        Schema::create('laporans', function (Blueprint $table) {
            $table->id();
            $table->string('tin', 20);
            $table->integer('tax_period_month');
            $table->integer('tax_period_year');
            $table->string('trx_code', 20)->default('Normal');
            $table->string('buyer_name', 100)->default('-');
            $table->string('buyer_id_opt', 10)->default('NIK');
            $table->string('buyer_id_number', 16)->default('0000000000000000');
            $table->string('good_service_opt', 5)->default('A');
            $table->string('serial_no', 50)->default('-');
            $table->date('transaction_date');
            $table->decimal('tax_base_selling_price', 15, 2);
            $table->decimal('other_tax_selling_price', 15, 2);
            $table->decimal('vat', 15, 2);
            $table->string('stlg', 10)->default('0');
            $table->string('info', 50)->default('ok');
            $table->longText('xml_content')->nullable();
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index(['tax_period_month', 'tax_period_year']);
            $table->index('tin');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporans');
    }
};