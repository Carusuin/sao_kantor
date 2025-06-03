<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    protected $table = 'laporans';

    protected $fillable = [
        'tin',
        'tax_period_month',
        'tax_period_year',
        'trx_code',
        'buyer_name',
        'buyer_id_opt',
        'buyer_id_number',
        'good_service_opt',
        'serial_no',
        'transaction_date',
        'tax_base_selling_price',
        'other_tax_selling_price',
        'vat',
        'stlg',
        'info',
        'xml_content'
    ];

    protected $casts = [
        'tax_base_selling_price' => 'decimal:2',
        'other_tax_selling_price' => 'decimal:2',
        'vat' => 'decimal:2',
        'transaction_date' => 'date'
    ];

    /**
     * Calculate Other Tax Selling Price
     * Formula: round(TaxBaseSellingPrice * 11 / 12)
     */
    public function calculateOtherTaxSellingPrice($taxBaseSellingPrice)
    {
        $result = $taxBaseSellingPrice * 11 / 12;
        return $this->customRound($result);
    }

    /**
     * Calculate VAT
     * Formula: round(TaxBaseSellingPrice * 12 / 100)
     */
    public function calculateVAT($taxBaseSellingPrice)
    {
        $result = $taxBaseSellingPrice * 12 / 100;
        return $this->customRound($result);
    }

    /**
     * Custom rounding function (1-4 down, 5-9 up)
     */
    private function customRound($number)
    {
        $decimal = $number - floor($number);
        $decimalFirstDigit = floor($decimal * 10);
        
        if ($decimalFirstDigit >= 1 && $decimalFirstDigit <= 4) {
            return floor($number);
        } else {
            return ceil($number);
        }
    }

    /**
     * Get last day of previous month
     */
    public static function getLastDayOfPreviousMonth()
    {
        return now()->subMonth()->endOfMonth()->format('Y-m-d');
    }

    /**
     * Generate XML content
     */
    public function generateXMLContent()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<RetailInvoiceBulk>' . "\n";
        $xml .= '    <TIN>' . $this->tin . '</TIN>' . "\n";
        $xml .= '    <TaxPeriodMonth>' . $this->tax_period_month . '</TaxPeriodMonth>' . "\n";
        $xml .= '    <TaxPeriodYear>' . $this->tax_period_year . '</TaxPeriodYear>' . "\n";
        $xml .= '    <ListOfRetailInvoice>' . "\n";
        $xml .= '        <RetailInvoice>' . "\n";
        $xml .= '            <TrxCode>' . $this->trx_code . '</TrxCode>' . "\n";
        $xml .= '            <BuyerName>' . $this->buyer_name . '</BuyerName>' . "\n";
        $xml .= '            <BuyerIdOpt>' . $this->buyer_id_opt . '</BuyerIdOpt>' . "\n";
        $xml .= '            <BuyerIdNumber>' . $this->buyer_id_number . '</BuyerIdNumber>' . "\n";
        $xml .= '            <GoodServiceOpt>' . $this->good_service_opt . '</GoodServiceOpt>' . "\n";
        $xml .= '            <SerialNo>' . $this->serial_no . '</SerialNo>' . "\n";
        $xml .= '            <TransactionDate>' . $this->transaction_date->format('Y-m-d') . '</TransactionDate>' . "\n";
        $xml .= '            <TaxBaseSellingPrice>' . number_format($this->tax_base_selling_price, 0, '', '') . '</TaxBaseSellingPrice>' . "\n";
        $xml .= '            <OtherTaxBaseSellingPrice>' . number_format($this->other_tax_selling_price, 0, '', '') . '</OtherTaxBaseSellingPrice>' . "\n";
        $xml .= '            <VAT>' . number_format($this->vat, 0, '', '') . '</VAT>' . "\n";
        $xml .= '            <STLG>' . $this->stlg . '</STLG>' . "\n";
        $xml .= '            <Info>' . $this->info . '</Info>' . "\n";
        $xml .= '        </RetailInvoice>' . "\n";
        $xml .= '    </ListOfRetailInvoice>' . "\n";
        $xml .= '</RetailInvoiceBulk>';
        
        return $xml;
    }
}