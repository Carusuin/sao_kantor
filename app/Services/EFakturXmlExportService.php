<?php

namespace App\Services;

use App\Models\Faktur;
use Carbon\Carbon;
use DOMDocument;
use DOMElement;
use Illuminate\Support\Collection;

class EFakturXmlExportService
{
    protected $dom;
    protected $root;
    
    public function __construct()
    {
        $this->dom = new DOMDocument('1.0', 'UTF-8');
        $this->dom->formatOutput = true;
    }
    
    /**
     * Export e-faktur to XML format
     */
    public function exportToXml(Collection $fakturs, array $options = []): string
    {
        $this->createRootElement($options);
        $this->addMetaData($fakturs, $options);
        $this->addFakturData($fakturs);
        
        return $this->dom->saveXML();
    }
    
    /**
     * Create root XML element
     */
    private function createRootElement(array $options): void
    {
        $rootName = $options['single'] ?? false ? 'EFakturReport' : 'EFakturReports';
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
    private function addMetaData(Collection $fakturs, array $options): void
    {
        $metaData = $this->dom->createElement('MetaData');
        $this->root->appendChild($metaData);
        
        // Report info
        $reportInfo = $this->dom->createElement('ReportInfo');
        $metaData->appendChild($reportInfo);
        
        $this->addTextElement($reportInfo, 'TotalRecords', $fakturs->count());
        $this->addTextElement($reportInfo, 'ExportDate', Carbon::now()->format('Y-m-d H:i:s'));
        $this->addTextElement($reportInfo, 'ExportedBy', auth()->user()->name ?? 'System');
        
        // Summary data
        if ($fakturs->count() > 0) {
            $summary = $this->dom->createElement('Summary');
            $metaData->appendChild($summary);
            
            $totalDPP = $fakturs->sum(function($faktur) {
                return $faktur->details->sum('dpp');
            });
            
            $this->addTextElement($summary, 'TotalDPP', number_format($totalDPP, 2, '.', ''));
            $this->addTextElement($summary, 'DateRange', 
                $fakturs->min('tanggal_faktur') . ' to ' . $fakturs->max('tanggal_faktur')
            );
        }
    }
    
    /**
     * Add faktur data to XML
     */
    private function addFakturData(Collection $fakturs): void
    {
        $dataContainer = $this->dom->createElement('Data');
        $this->root->appendChild($dataContainer);
        
        foreach ($fakturs as $faktur) {
            $this->addSingleFaktur($dataContainer, $faktur);
        }
    }
    
    /**
     * Add single faktur record to XML
     */
    private function addSingleFaktur(DOMElement $container, Faktur $faktur): void
    {
        $record = $this->dom->createElement('EFakturRecord');
        $container->appendChild($record);
        
        // Basic Info
        $basicInfo = $this->dom->createElement('BasicInfo');
        $record->appendChild($basicInfo);
        
        $this->addTextElement($basicInfo, 'ID', $faktur->id);
        $this->addTextElement($basicInfo, 'TanggalFaktur', $faktur->tanggal_faktur->format('Y-m-d'));
        $this->addTextElement($basicInfo, 'JenisFaktur', $faktur->jenis_faktur);
        $this->addTextElement($basicInfo, 'KodeTransaksi', $faktur->kode_transaksi);
        $this->addTextElement($basicInfo, 'NomorFaktur', $faktur->nomor_faktur);
        
        // Buyer Info
        $buyerInfo = $this->dom->createElement('BuyerInfo');
        $record->appendChild($buyerInfo);
        
        $this->addTextElement($buyerInfo, 'NPWP_NIK', $faktur->npwp_nik_pembeli);
        $this->addTextElement($buyerInfo, 'JenisID', $faktur->jenis_id_pembeli);
        $this->addTextElement($buyerInfo, 'NamaPembeli', $faktur->nama_pembeli);
        $this->addTextElement($buyerInfo, 'AlamatPembeli', $faktur->alamat_pembeli);
        $this->addTextElement($buyerInfo, 'EmailPembeli', $faktur->email_pembeli);
        
        // Transaction Details
        $details = $this->dom->createElement('Details');
        $record->appendChild($details);
        
        foreach ($faktur->details as $detail) {
            $detailElement = $this->dom->createElement('Detail');
            $details->appendChild($detailElement);
            
            $this->addTextElement($detailElement, 'Baris', $detail->baris);
            $this->addTextElement($detailElement, 'NamaBarangJasa', $detail->nama_barang_jasa);
            $this->addTextElement($detailElement, 'Jumlah', number_format($detail->jumlah_barang_jasa, 2, '.', ''));
            $this->addTextElement($detailElement, 'HargaSatuan', number_format($detail->harga_satuan, 2, '.', ''));
            $this->addTextElement($detailElement, 'DPP', number_format($detail->dpp, 2, '.', ''));
            $this->addTextElement($detailElement, 'PPN', number_format($detail->ppn, 2, '.', ''));
            $this->addTextElement($detailElement, 'PPNBM', number_format($detail->ppnbm, 2, '.', ''));
        }
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
        
        if (isset($options['single']) && $options['single']) {
            return "efaktur_report_single_{$timestamp}.xml";
        }
        
        return "efaktur_report_{$timestamp}.xml";
    }
} 