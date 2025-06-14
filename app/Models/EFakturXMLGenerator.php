<?php

namespace App\Services;

use App\Models\BarangJasa;
use App\Models\LaporanFaktur;
use Illuminate\Support\Collection;
use SimpleXMLElement;

class EFakturXmlGenerator
{
    /**
     * Generate XML file untuk e-Faktur berdasarkan dokumen_id
     */
    public function generateByDokumen(int $dokumenId): string
    {
        $items = LaporanFaktur::byDokumen($dokumenId)
                          ->orderBy('baris')
                          ->get();

        if ($items->isEmpty()) {
            throw new \Exception('Tidak ada data barang/jasa untuk dokumen ID: ' . $dokumenId);
        }

        return $this->generateXml($items);
    }

    /**
     * Generate XML dari collection BarangJasa
     */
    public function generateXml(Collection $items): string
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><BARANG_JASA></BARANG_JASA>');

        foreach ($items as $item) {
            $this->addItemToXml($xml, $item);
        }

        return $this->formatXml($xml->asXML());
    }

    /**
     * Generate XML dengan format manual (untuk kontrol lebih detail)
     */
    public function generateManualXml(Collection $items): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<BARANG_JASA>' . "\n";

        foreach ($items as $item) {
            $xml .= $this->generateItemXml($item);
        }

        $xml .= '</BARANG_JASA>';

        return $xml;
    }

    /**
     * Generate XML untuk single item
     */
    public function generateSingleItemXml(LaporanFaktur $item): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<BARANG_JASA>' . "\n";
        $xml .= $this->generateItemXml($item);
        $xml .= '</BARANG_JASA>';

        return $xml;
    }

    /**
     * Generate CSV format untuk e-Faktur (alternatif XML)
     */
    public function generateCsv(Collection $items): string
    {
        $csv = "BARIS,JENIS_BARANG_JASA,KODE_BARANG_JASA,NAMA_BARANG_JASA,NAMA_SATUAN_UKUR,HARGA_SATUAN,JUMLAH_BARANG_JASA,TOTAL_DISKON,DPP,DPP_NILAI_LAIN,TARIF_PPN,PPN,TARIF_PPNBM,PPNBM\n";

        foreach ($items as $item) {
            $csv .= implode(',', [
                $item->baris,
                $item->jenis_barang_jasa,
                '"' . str_replace('"', '""', $item->kode_barang_jasa) . '"',
                '"' . str_replace('"', '""', $item->nama_barang_jasa) . '"',
                '"' . str_replace('"', '""', $item->nama_satuan_ukur) . '"',
                number_format($item->harga_satuan, 2, '.', ''),
                number_format($item->jumlah_barang_jasa, 2, '.', ''),
                number_format($item->total_diskon, 2, '.', ''),
                number_format($item->dpp, 2, '.', ''),
                number_format($item->dpp_nilai_lain, 2, '.', ''),
                number_format($item->tarif_ppn, 2, '.', ''),
                number_format($item->ppn, 2, '.', ''),
                number_format($item->tarif_ppnbm, 2, '.', ''),
                number_format($item->ppnbm, 2, '.', '')
            ]) . "\n";
        }

        return $csv;
    }

    /**
     * Validate XML structure
     */
    public function validateXml(string $xmlContent): array
    {
        $errors = [];

        try {
            $xml = new SimpleXMLElement($xmlContent);
            
            if ($xml->getName() !== 'BARANG_JASA') {
                $errors[] = 'Root element harus bernama BARANG_JASA';
            }

            foreach ($xml->ITEM as $index => $item) {
                $itemErrors = $this->validateXmlItem($item, $index + 1);
                $errors = array_merge($errors, $itemErrors);
            }

        } catch (\Exception $e) {
            $errors[] = 'Format XML tidak valid: ' . $e->getMessage();
        }

        return $errors;
    }

    /**
     * Parse XML ke array untuk import
     */
    public function parseXmlToArray(string $xmlContent): array
    {
        $xml = new SimpleXMLElement($xmlContent);
        $items = [];

        foreach ($xml->ITEM as $item) {
            $items[] = [
                'baris' => (int) $item->BARIS,
                'jenis_barang_jasa' => (string) $item->JENIS_BARANG_JASA,
                'kode_barang_jasa' => (string) $item->KODE_BARANG_JASA,
                'nama_barang_jasa' => (string) $item->NAMA_BARANG_JASA,
                'nama_satuan_ukur' => (string) $item->NAMA_SATUAN_UKUR,
                'harga_satuan' => (float) $item->HARGA_SATUAN,
                'jumlah_barang_jasa' => (float) $item->JUMLAH_BARANG_JASA,
                'total_diskon' => (float) $item->TOTAL_DISKON,
                'dpp' => (float) $item->DPP,
                'dpp_nilai_lain' => (float) $item->DPP_NILAI_LAIN,
                'tarif_ppn' => (float) $item->TARIF_PPN,
                'ppn' => (float) $item->PPN,
                'tarif_ppnbm' => (float) $item->TARIF_PPNBM,
                'ppnbm' => (float) $item->PPNBM,
            ];
        }

        return $items;
    }

    /**
     * Private method untuk menambah item ke XML
     */
    private function addItemToXml(SimpleXMLElement $xml, LaporanFaktur $item): void
    {
        $itemXml = $xml->addChild('ITEM');
        $itemXml->addChild('BARIS', $item->baris);
        $itemXml->addChild('JENIS_BARANG_JASA', $item->jenis_barang_jasa);
        $itemXml->addChild('KODE_BARANG_JASA', htmlspecialchars($item->kode_barang_jasa));
        $itemXml->addChild('NAMA_BARANG_JASA', htmlspecialchars($item->nama_barang_jasa));
        $itemXml->addChild('NAMA_SATUAN_UKUR', htmlspecialchars($item->nama_satuan_ukur));
        $itemXml->addChild('HARGA_SATUAN', number_format($item->harga_satuan, 2, '.', ''));
        $itemXml->addChild('JUMLAH_BARANG_JASA', number_format($item->jumlah_barang_jasa, 2, '.', ''));
        $itemXml->addChild('TOTAL_DISKON', number_format($item->total_diskon, 2, '.', ''));
        $itemXml->addChild('DPP', number_format($item->dpp, 2, '.', ''));
        $itemXml->addChild('DPP_NILAI_LAIN', number_format($item->dpp_nilai_lain, 2, '.', ''));
        $itemXml->addChild('TARIF_PPN', number_format($item->tarif_ppn, 2, '.', ''));
        $itemXml->addChild('PPN', number_format($item->ppn, 2, '.', ''));
        $itemXml->addChild('TARIF_PPNBM', number_format($item->tarif_ppnbm, 2, '.', ''));
        $itemXml->addChild('PPNBM', number_format($item->ppnbm, 2, '.', ''));
    }

    /**
     * Generate XML string untuk single item
     */
    private function generateItemXml(LaporanFaktur $item): string
    {
        $xml = '  <ITEM>' . "\n";
        $xml .= '    <BARIS>' . $item->baris . '</BARIS>' . "\n";
        $xml .= '    <JENIS_BARANG_JASA>' . $item->jenis_barang_jasa . '</JENIS_BARANG_JASA>' . "\n";
        $xml .= '    <KODE_BARANG_JASA>' . htmlspecialchars($item->kode_barang_jasa) . '</KODE_BARANG_JASA>' . "\n";
        $xml .= '    <NAMA_BARANG_JASA>' . htmlspecialchars($item->nama_barang_jasa) . '</NAMA_BARANG_JASA>' . "\n";
        $xml .= '    <NAMA_SATUAN_UKUR>' . htmlspecialchars($item->nama_satuan_ukur) . '</NAMA_SATUAN_UKUR>' . "\n";
        $xml .= '    <HARGA_SATUAN>' . number_format($item->harga_satuan, 2, '.', '') . '</HARGA_SATUAN>' . "\n";
        $xml .= '    <JUMLAH_BARANG_JASA>' . number_format($item->jumlah_barang_jasa, 2, '.', '') . '</JUMLAH_BARANG_JASA>' . "\n";
        $xml .= '    <TOTAL_DISKON>' . number_format($item->total_diskon, 2, '.', '') . '</TOTAL_DISKON>' . "\n";
        $xml .= '    <DPP>' . number_format($item->dpp, 2, '.', '') . '</DPP>' . "\n";
        $xml .= '    <DPP_NILAI_LAIN>' . number_format($item->dpp_nilai_lain, 2, '.', '') . '</DPP_NILAI_LAIN>' . "\n";
        $xml .= '    <TARIF_PPN>' . number_format($item->tarif_ppn, 2, '.', '') . '</TARIF_PPN>' . "\n";
        $xml .= '    <PPN>' . number_format($item->ppn, 2, '.', '') . '</PPN>' . "\n";
        $xml .= '    <TARIF_PPNBM>' . number_format($item->tarif_ppnbm, 2, '.', '') . '</TARIF_PPNBM>' . "\n";
        $xml .= '    <PPNBM>' . number_format($item->ppnbm, 2, '.', '') . '</PPNBM>' . "\n";
        $xml .= '  </ITEM>' . "\n";

        return $xml;
    }

    /**
     * Format XML dengan indentasi yang rapi
     */
    private function formatXml(string $xml): string
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml);
        
        return $dom->saveXML();
    }

    /**
     * Validasi item XML
     */
    private function validateXmlItem(SimpleXMLElement $item, int $index): array
    {
        $errors = [];
        $requiredFields = [
            'BARIS', 'JENIS_BARANG_JASA', 'KODE_BARANG_JASA', 
            'NAMA_BARANG_JASA', 'NAMA_SATUAN_UKUR', 'HARGA_SATUAN',
            'JUMLAH_BARANG_JASA', 'DPP', 'TARIF_PPN', 'PPN'
        ];

        foreach ($requiredFields as $field) {
            if (!isset($item->$field) || trim((string) $item->$field) === '') {
                $errors[] = "Item {$index}: Field {$field} wajib diisi";
            }
        }

        // Validasi jenis barang/jasa
        if (isset($item->JENIS_BARANG_JASA) && !in_array((string) $item->JENIS_BARANG_JASA, ['B', 'J'])) {
            $errors[] = "Item {$index}: JENIS_BARANG_JASA harus B (Barang) atau J (Jasa)";
        }

        // Validasi nilai numerik
        $numericFields = ['BARIS', 'HARGA_SATUAN', 'JUMLAH_BARANG_JASA', 'DPP', 'TARIF_PPN', 'PPN'];
        foreach ($numericFields as $field) {
            if (isset($item->$field) && !is_numeric((string) $item->$field)) {
                $errors[] = "Item {$index}: Field {$field} harus berupa angka";
            }
        }

        return $errors;
    }
}