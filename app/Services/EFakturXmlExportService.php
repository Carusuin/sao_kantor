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
    protected $fakturs;
    
    public function __construct()
    {
        $this->dom = new DOMDocument('1.0', 'utf-8');
        $this->dom->formatOutput = true;
    }
    
    /**
     * Export e-faktur to XML format
     */
    public function exportToXml(Collection $fakturs, array $options = []): string
    {
        $this->fakturs = $fakturs;
        $this->createRootElement();
        $this->addTinElement();
        $this->addFakturData($fakturs);
        
        return $this->dom->saveXML();
    }
    
    /**
     * Create root XML element
     */
    private function createRootElement(): void
    {
        $this->root = $this->dom->createElement('TaxInvoiceBulk');
        $this->root->setAttribute('xmlns:xsd', 'http://www.w3.org/2001/XMLSchema');
        $this->root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $this->dom->appendChild($this->root);
    }
    
    /**
     * Add TIN element
     */
    private function addTinElement(): void
    {
        // Get the first faktur to get the seller's NPWP
        $firstFaktur = $this->fakturs->first();
        $tin = $this->dom->createElement('TIN', $firstFaktur->npwp_penjual);
        $this->root->appendChild($tin);
    }
    
    /**
     * Add faktur data to XML
     */
    private function addFakturData(Collection $fakturs): void
    {
        $listOfTaxInvoice = $this->dom->createElement('ListOfTaxInvoice');
        $this->root->appendChild($listOfTaxInvoice);
        
        foreach ($fakturs as $faktur) {
            $taxInvoice = $this->createTaxInvoiceElement($faktur);
            $listOfTaxInvoice->appendChild($taxInvoice);
        }
    }
    
    /**
     * Create single tax invoice element
     */
    private function createTaxInvoiceElement(Faktur $faktur): DOMElement
    {
        $taxInvoice = $this->dom->createElement('TaxInvoice');
        
        // Basic invoice info
        $this->addTextElement($taxInvoice, 'TaxInvoiceDate', $faktur->tanggal_faktur->format('Y-m-d'));
        $this->addTextElement($taxInvoice, 'TaxInvoiceOpt', 'Normal');
        $this->addTextElement($taxInvoice, 'TrxCode', $faktur->kode_transaksi ?? '04');
        $this->addTextElement($taxInvoice, 'AddInfo', '');
        $this->addTextElement($taxInvoice, 'CustomDoc', '');
        $this->addTextElement($taxInvoice, 'RefDesc', $faktur->referensi);
        $this->addTextElement($taxInvoice, 'FacilityStamp', '');
        
        // Seller info
        $this->addTextElement($taxInvoice, 'SellerIDTKU', $faktur->id_tku_penjual ?? '0023694821541000000000');
        
        // Buyer info
        $this->addTextElement($taxInvoice, 'BuyerTin', $faktur->npwp_nik_pembeli);
        $this->addTextElement($taxInvoice, 'BuyerDocument', 'TIN');
        $this->addTextElement($taxInvoice, 'BuyerCountry', $this->getCountryCode($faktur->negara_pembeli));
        $this->addTextElement($taxInvoice, 'BuyerDocumentNumber', $faktur->referensi);
        $this->addTextElement($taxInvoice, 'BuyerName', $faktur->nama_pembeli);
        $this->addTextElement($taxInvoice, 'BuyerAdress', $faktur->alamat_pembeli);
        $this->addTextElement($taxInvoice, 'BuyerEmail', $faktur->email_pembeli);
        $this->addTextElement($taxInvoice, 'BuyerIDTKU', $faktur->id_tku_pembeli);
        
        // List of goods/services
        $listOfGoodService = $this->dom->createElement('ListOfGoodService');
        $taxInvoice->appendChild($listOfGoodService);
        
        foreach ($faktur->details as $detail) {
            $goodService = $this->createGoodServiceElement($detail);
            $listOfGoodService->appendChild($goodService);
        }
        
        return $taxInvoice;
    }
    
    /**
     * Convert country name to ISO code
     */
    private function getCountryCode($countryName): string
    {
        $countryMapping = [
            'Indonesia' => 'IDN',
            'Singapore' => 'SGP',
            'Malaysia' => 'MYS',
            'Thailand' => 'THA',
            'Vietnam' => 'VNM',
            'Philippines' => 'PHL',
            'Brunei' => 'BRN',
            'Cambodia' => 'KHM',
            'Laos' => 'LAO',
            'Myanmar' => 'MMR',
            'East Timor' => 'TLS'
        ];
        
        return $countryMapping[$countryName] ?? 'IDN';
    }
    
    /**
     * Create good service element
     */
    private function createGoodServiceElement($detail): DOMElement
    {
        $goodService = $this->dom->createElement('GoodService');
        
        // OPT langsung dari kolom barang_jasa
        $opt = $detail->barang_jasa;
        
        $this->addTextElement($goodService, 'Opt', $opt);
        $this->addTextElement($goodService, 'Code', $detail->kode_barang_jasa ?? '000000');
        $this->addTextElement($goodService, 'Name', $detail->nama_barang_jasa);
        $this->addTextElement($goodService, 'Unit', $detail->nama_satuan_ukur);
        $this->addTextElement($goodService, 'Price', (int)$detail->harga_satuan);
        $this->addTextElement($goodService, 'Qty', (int)$detail->jumlah_barang_jasa);
        $this->addTextElement($goodService, 'TotalDiscount', (int)($detail->total_diskon ?? 0));
        
        // Calculate tax base
        $taxBase = $detail->harga_satuan * $detail->jumlah_barang_jasa - ($detail->total_diskon ?? 0);
        $this->addTextElement($goodService, 'TaxBase', (int)$taxBase);
        
        // Calculate other tax base (before VAT)
        $otherTaxBase = round($taxBase / (1 + ($detail->tarif_ppn / 100)), 0);
        $this->addTextElement($goodService, 'OtherTaxBase', (int)$otherTaxBase);
        
        $this->addTextElement($goodService, 'VATRate', (int)($detail->tarif_ppn ?? 12));
        $this->addTextElement($goodService, 'VAT', (int)($detail->ppn ?? ($taxBase * 0.12)));
        $this->addTextElement($goodService, 'STLGRate', (int)($detail->tarif_ppnbm ?? 0));
        $this->addTextElement($goodService, 'STLG', (int)($detail->ppnbm ?? 0));
        
        return $goodService;
    }
    
    /**
     * Determine Opt type based on item name
     */
    private function determineOptType($itemName): string
    {
        $serviceKeywords = ['VACUUM', 'B/P', 'LAS', 'SERVICE', 'JASA'];
        
        foreach ($serviceKeywords as $keyword) {
            if (stripos($itemName, $keyword) !== false) {
                return 'B'; // Service
            }
        }
        
        return 'A'; // Goods (default)
    }
    
    /**
     * Get unit code based on unit name
     */
    private function getUnitCode($unitName): string
    {
        $unitMapping = [
            'PCS' => 'UM.0018',
            'UNIT' => 'UM.0018',
            'BUAH' => 'UM.0018',
            'KG' => 'UM.0003',
            'LITER' => 'UM.0003',
            'BOTOL' => 'UM.0003',
            'JAM' => 'UM.0030',
            'HARI' => 'UM.0030',
            'PAKET' => 'UM.0021',
            'SET' => 'UM.0021'
        ];
        
        $unitName = strtoupper($unitName ?? 'PCS');
        return $unitMapping[$unitName] ?? 'UM.0018';
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
        $timestamp = Carbon::now()->format('Ymd_His');
        return "tax_invoice_bulk_{$timestamp}.xml";
    }
} 