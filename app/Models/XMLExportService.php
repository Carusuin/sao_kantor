<?php
// app/Services/XmlExportService.php

namespace App\Services;

use App\Models\Laporan;
use Carbon\Carbon;
use DOMDocument;
use DOMElement;
use Illuminate\Support\Collection;

class XmlExportService
{
    protected $dom;
    protected $root;
    
    public function __construct()
    {
        $this->dom = new DOMDocument('1.0', 'UTF-8');
        $this->dom->formatOutput = true;
    }
    
    /**
     * Export laporan to XML format
     */
    public function exportToXml(Collection $laporans, array $options = []): string
    {
        $this->createRootElement($options);
        $this->addMetaData($laporans, $options);
        $this->addLaporanData($laporans);
        
        return $this->dom->saveXML();
    }
    
    /**
     * Export single laporan to XML
     */
    public function exportSingleToXml(Laporan $laporan): string
    {
        $collection = collect([$laporan]);
        return $this->exportToXml($collection, ['single' => true]);
    }
    
    /**
     * Export laporan by period to XML
     */
    public function exportByPeriod(int $month, int $year): string
    {
        $laporans = Laporan::where('tax_period_month', $month)
                           ->where('tax_period_year', $year)
                           ->orderBy('transaction_date')
                           ->get();
        
        $options = [
            'period_month' => $month,
            'period_year' => $year,
            'by_period' => true
        ];
        
        return $this->exportToXml($laporans, $options);
    }
    
    /**
     * Create root XML element
     */
    private function createRootElement(array $options): void
    {
        $rootName = $options['single'] ?? false ? 'TaxReport' : 'TaxReports';
        $this->root = $this->dom->createElement($rootName);
        $this->dom->appendChild($this->root);
        
        // Add XML namespace and schema info
        $this->root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $this->root->setAttribute('version', '1.0');
        $this->root->setAttribute('generated', Carbon::now()->toISOString());
    }
    
    /**
     * Add metadata to XML
     */
    private function addMetaData(Collection $laporans, array $options): void
    {
        $metaData = $this->dom->createElement('MetaData');
        $this->root->appendChild($metaData);
        
        // Report info
        $reportInfo = $this->dom->createElement('ReportInfo');
        $metaData->appendChild($reportInfo);
        
        $this->addTextElement($reportInfo, 'TotalRecords', $laporans->count());
        $this->addTextElement($reportInfo, 'ExportDate', Carbon::now()->format('Y-m-d H:i:s'));
        $this->addTextElement($reportInfo, 'ExportedBy', auth()->user()->name ?? 'System');
        
        if (isset($options['by_period']) && $options['by_period']) {
            $this->addTextElement($reportInfo, 'PeriodMonth', $options['period_month']);
            $this->addTextElement($reportInfo, 'PeriodYear', $options['period_year']);
            $this->addTextElement($reportInfo, 'PeriodName', 
                DateTime::createFromFormat('!m', $options['period_month'])->format('F') . ' ' . $options['period_year']
            );
        }
        
        // Summary data
        if ($laporans->count() > 0) {
            $summary = $this->dom->createElement('Summary');
            $metaData->appendChild($summary);
            
            $this->addTextElement($summary, 'TotalTaxBase', $laporans->sum('tax_base_selling_price'));
            $this->addTextElement($summary, 'TotalVAT', $laporans->sum('vat'));
            $this->addTextElement($summary, 'TotalOtherTax', $laporans->sum('other_tax_selling_price'));
            $this->addTextElement($summary, 'DateRange', 
                $laporans->min('transaction_date') . ' to ' . $laporans->max('transaction_date')
            );
        }
    }
    
    /**
     * Add laporan data to XML
     */
    private function addLaporanData(Collection $laporans): void
    {
        $dataContainer = $this->dom->createElement('Data');
        $this->root->appendChild($dataContainer);
        
        foreach ($laporans as $laporan) {
            $this->addSingleLaporan($dataContainer, $laporan);
        }
    }
    
    /**
     * Add single laporan record to XML
     */
    private function addSingleLaporan(DOMElement $container, Laporan $laporan): void
    {
        $record = $this->dom->createElement('TaxRecord');
        $container->appendChild($record);
        
        // Basic Info
        $basicInfo = $this->dom->createElement('BasicInfo');
        $record->appendChild($basicInfo);
        
        $this->addTextElement($basicInfo, 'ID', $laporan->id);
        $this->addTextElement($basicInfo, 'TaxPeriodMonth', $laporan->tax_period_month);
        $this->addTextElement($basicInfo, 'TaxPeriodYear', $laporan->tax_period_year);
        $this->addTextElement($basicInfo, 'TrxCode', $laporan->trx_code);
        $this->addTextElement($basicInfo, 'TransactionDate', $laporan->transaction_date->format('Y-m-d'));
        $this->addTextElement($basicInfo, 'CreatedAt', $laporan->created_at->toISOString());
        $this->addTextElement($basicInfo, 'UpdatedAt', $laporan->updated_at->toISOString());
        
        // Buyer Info
        $buyerInfo = $this->dom->createElement('BuyerInfo');
        $record->appendChild($buyerInfo);
        
        $this->addTextElement($buyerInfo, 'Name', $laporan->buyer_name);
        $this->addTextElement($buyerInfo, 'IDOption', $laporan->buyer_id_opt);
        $this->addTextElement($buyerInfo, 'IDNumber', $laporan->buyer_id_number);
        
        // Transaction Info
        $transactionInfo = $this->dom->createElement('TransactionInfo');
        $record->appendChild($transactionInfo);
        
        $this->addTextElement($transactionInfo, 'GoodServiceOption', $laporan->good_service_opt);
        $this->addTextElement($transactionInfo, 'SerialNumber', $laporan->serial_no);
        $this->addTextElement($transactionInfo, 'STLG', $laporan->stlg);
        $this->addTextElement($transactionInfo, 'Info', $laporan->info ?? 'ok');
        
        // Financial Info
        $financialInfo = $this->dom->createElement('FinancialInfo');
        $record->appendChild($financialInfo);
        
        $this->addTextElement($financialInfo, 'TaxBaseSellingPrice', number_format($laporan->tax_base_selling_price, 2, '.', ''));
        $this->addTextElement($financialInfo, 'OtherTaxSellingPrice', number_format($laporan->other_tax_selling_price ?? 0, 2, '.', ''));
        $this->addTextElement($financialInfo, 'VAT', number_format($laporan->vat, 2, '.', ''));
        
        // Calculate totals
        $totalAmount = $laporan->tax_base_selling_price + ($laporan->other_tax_selling_price ?? 0) + $laporan->vat;
        $this->addTextElement($financialInfo, 'TotalAmount', number_format($totalAmount, 2, '.', ''));
    }
    
    /**
     * Add text element helper
     */
    private function addTextElement(DOMElement $parent, string $name, $value): void
    {
        $element = $this->dom->createElement($name);
        $element->appendChild($this->dom->createTextNode((string) $value));
        $parent->appendChild($element);
    }
    
    /**
     * Generate XML filename
     */
    public function generateFilename(array $options = []): string
    {
        $timestamp = Carbon::now()->format('YmdHis');
        
        if (isset($options['by_period']) && $options['by_period']) {
            return "tax_report_{$options['period_year']}_{$options['period_month']:02d}_{$timestamp}.xml";
        }
        
        if (isset($options['single']) && $options['single']) {
            return "tax_report_single_{$timestamp}.xml";
        }
        
        return "tax_report_{$timestamp}.xml";
    }
    
    /**
     * Validate XML against schema (if schema file exists)
     */
    public function validateXml(string $xmlContent, string $schemaPath = null): array
    {
        $errors = [];
        
        try {
            $dom = new DOMDocument();
            $dom->loadXML($xmlContent);
            
            if ($schemaPath && file_exists($schemaPath)) {
                libxml_use_internal_errors(true);
                
                if (!$dom->schemaValidate($schemaPath)) {
                    $errors = libxml_get_errors();
                }
                
                libxml_clear_errors();
            }
        } catch (\Exception $e) {
            $errors[] = (object) ['message' => $e->getMessage()];
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Export with custom formatting options
     */
    public function exportWithOptions(Collection $laporans, array $customOptions = []): string
    {
        $this->createRootElement($customOptions);
        
        if ($customOptions['include_metadata'] ?? true) {
            $this->addMetaData($laporans, $customOptions);
        }
        
        if ($customOptions['group_by_period'] ?? false) {
            $this->addGroupedByPeriod($laporans);
        } else {
            $this->addLaporanData($laporans);
        }
        
        return $this->dom->saveXML();
    }
    
    /**
     * Add data grouped by period
     */
    private function addGroupedByPeriod(Collection $laporans): void
    {
        $dataContainer = $this->dom->createElement('Data');
        $this->root->appendChild($dataContainer);
        
        $groupedByPeriod = $laporans->groupBy(function ($laporan) {
            return $laporan->tax_period_year . '-' . str_pad($laporan->tax_period_month, 2, '0', STR_PAD_LEFT);
        });
        
        foreach ($groupedByPeriod as $period => $periodLaporans) {
            [$year, $month] = explode('-', $period);
            
            $periodGroup = $this->dom->createElement('PeriodGroup');
            $dataContainer->appendChild($periodGroup);
            
            $periodGroup->setAttribute('year', $year);
            $periodGroup->setAttribute('month', $month);
            $periodGroup->setAttribute('count', $periodLaporans->count());
            
            foreach ($periodLaporans as $laporan) {
                $this->addSingleLaporan($periodGroup, $laporan);
            }
        }
    }
}