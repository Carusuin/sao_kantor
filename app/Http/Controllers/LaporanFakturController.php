<?php

namespace App\Http\Controllers;

use App\Models\Faktur;
use App\Models\DetailFaktur;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;

class LaporanFakturController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Faktur::query();

        // Filter dan sorting dasar untuk tabel Faktur
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_pembeli', 'like', '%' . $request->search . '%')
                  ->orWhere('npwp_nik_pembeli', 'like', '%' . $request->search . '%')
                  ->orWhere('kode_transaksi', 'like', '%' . $request->search . '%');
            });
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 15);
        $fakturs = $query->paginate($perPage); // Menggunakan nama variabel fakturs

        return view('laporanfaktur.index', compact('fakturs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $taxPeriodMonth = now()->month;
        $taxPeriodYear = now()->year;
        $transactionDate = now()->format('Y-m-d');

        $units = [
            ['code' => 'UM.0003', 'name' => 'Kilogram'],
            ['code' => 'UM.0004', 'name' => 'Gram'],
            ['code' => 'UM.0005', 'name' => 'Karat'],
            ['code' => 'UM.0001', 'name' => 'Metrik Ton'],
            ['code' => 'UM.0002', 'name' => 'Wet Ton'],
            ['code' => 'UM.0006', 'name' => 'Kiloliter'],
            ['code' => 'UM.0007', 'name' => 'Liter'],
            ['code' => 'UM.0008', 'name' => 'Barrel'],
            ['code' => 'UM.0009', 'name' => 'MMBTU'],
            ['code' => 'UM.0010', 'name' => 'Ampere'],
            ['code' => 'UM.0011', 'name' => 'Sentimeter Kubik'],
            ['code' => 'UM.0012', 'name' => 'Meter Persegi'],
            ['code' => 'UM.0013', 'name' => 'Meter'],
            ['code' => 'UM.0014', 'name' => 'Inches'],
            ['code' => 'UM.0015', 'name' => 'Sentimeter'],
            ['code' => 'UM.0016', 'name' => 'Yard'],
            ['code' => 'UM.0017', 'name' => 'Lusin'],
            ['code' => 'UM.0018', 'name' => 'Unit'],
            ['code' => 'UM.0019', 'name' => 'Set'],
            ['code' => 'UM.0020', 'name' => 'Lembar'],
            ['code' => 'UM.0021', 'name' => 'Piece'],
            ['code' => 'UM.0022', 'name' => 'Boks'],
            ['code' => 'UM.0023', 'name' => 'Tahun'],
            ['code' => 'UM.0024', 'name' => 'Bulan'],
            ['code' => 'UM.0025', 'name' => 'Minggu'],
            ['code' => 'UM.0026', 'name' => 'Hari'],
            ['code' => 'UM.0027', 'name' => 'Jam'],
            ['code' => 'UM.0028', 'name' => 'Menit'],
            ['code' => 'UM.0029', 'name' => 'Persen'],
            ['code' => 'UM.0030', 'name' => 'Kegiatan'],
            ['code' => 'UM.0031', 'name' => 'Laporan'],
            ['code' => 'UM.0032', 'name' => 'Bahan'],
            ['code' => 'UM.0033', 'name' => 'Lainnya'],
        ];

        return view('laporanfaktur.create', compact('taxPeriodMonth', 'taxPeriodYear', 'transactionDate', 'units'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validasi data faktur
            $fakturData = $request->validate([
                'tanggal_faktur' => 'required|date',
                'jenis_faktur' => 'required|string',
                'kode_transaksi' => 'required|string',
                'referensi' => 'nullable|string',
                'alamat_dokumen' => 'required|string',
                'id_tku_dokumen' => 'required|string',
                'npwp_penjual' => 'required|string',
                'npwp_pembeli' => 'required|string',
                'buyer_id_type' => 'required|string',
                'negara_pembeli' => 'required|string',
                'nomor_dokumen_pembeli' => 'nullable|string',
                'nama_pembeli' => 'required|string',
                'alamat_pembeli' => 'required|string',
                'email_pembeli' => 'nullable|email',
                'id_tku_pembeli' => 'required|string',
                'uang_muka' => 'boolean',
                'pelunasan' => 'boolean',
                'nomor_faktur' => 'required|string|max:255|regex:/^[A-Z0-9.-]+$/',
            ]);

            // Buat record faktur
            $faktur = Faktur::create([
                'tanggal_faktur' => $fakturData['tanggal_faktur'],
                'jenis_faktur' => $fakturData['jenis_faktur'],
                'kode_transaksi' => $fakturData['kode_transaksi'],
                'referensi' => $fakturData['referensi'],
                'alamat_penjual' => $fakturData['alamat_dokumen'],
                'id_tku_penjual' => $fakturData['id_tku_dokumen'],
                'npwp_penjual' => $fakturData['npwp_penjual'],
                'npwp_nik_pembeli' => $fakturData['npwp_pembeli'],
                'jenis_id_pembeli' => $fakturData['buyer_id_type'],
                'negara_pembeli' => $fakturData['negara_pembeli'],
                'nomor_dokumen_pembeli' => $fakturData['nomor_dokumen_pembeli'],
                'nama_pembeli' => $fakturData['nama_pembeli'],
                'alamat_pembeli' => $fakturData['alamat_pembeli'],
                'email_pembeli' => $fakturData['email_pembeli'],
                'id_tku_pembeli' => $fakturData['id_tku_pembeli'],
                'uang_muka' => $request->boolean('uang_muka'),
                'pelunasan' => $request->boolean('pelunasan'),
                'nomor_faktur' => $request->input('nomor_faktur'),
            ]);

            // Validasi dan simpan detail item
            if ($request->has('items')) {
                foreach ($request->items as $index => $item) {
                    $detailData = [
                        'faktur_id' => $faktur->id,
                        'baris' => $index + 1,
                        'barang_jasa' => $item['jenis_barang_jasa'],
                        'kode_barang_jasa' => $item['kode_barang_jasa'],
                        'nama_barang_jasa' => $item['nama_barang_jasa'],
                        'nama_satuan_ukur' => $item['nama_satuan_ukur'],
                        'harga_satuan' => $item['harga_satuan'],
                        'jumlah_barang_jasa' => $item['jumlah_barang_jasa'],
                        'total_diskon' => $item['total_diskon'] ?? 0,
                        'dpp' => $item['dpp'] ?? 0,
                        'dpp_nilai_lain' => $item['dpp_nilai_lain'] ?? 0,
                        'tarif_ppn' => $item['tarif_ppn'] ?? 11.00,
                        'ppn' => $item['ppn'] ?? 0,
                        'tarif_ppnbm' => $item['tarif_ppnbm'] ?? 0,
                        'ppnbm' => $item['ppnbm'] ?? 0,
                    ];

                    DetailFaktur::create($detailData);
                }
            }

            return redirect()->route('laporan_faktur.index')
                           ->with('success', 'Faktur berhasil disimpan');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Faktur $laporanFaktur)
    {
        $laporanFaktur->load('details'); // Eager load detail faktur

        // Meneruskan model Faktur sebagai 'laporan' untuk konsistensi dengan tampilan yang ada
        return view('laporanfaktur.laporan_show', compact('laporanFaktur'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Faktur $laporanFaktur)
    {
        $laporanFaktur->load('details'); // Memuat detail untuk editing

        // Meneruskan model Faktur sebagai 'laporan' untuk konsistensi dengan tampilan yang ada
        return view('laporanfaktur.laporan_edit', compact('laporanFaktur'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Faktur $laporanFaktur)
    {
        try {
            // Validasi data faktur (mirip dengan store, tapi untuk update)
            $fakturData = $request->validate([
                'tanggal_faktur' => 'required|date',
                'jenis_faktur' => 'required|string',
                'kode_transaksi' => 'required|string',
                'referensi' => 'nullable|string',
                'alamat_dokumen' => 'required|string',
                'id_tku_dokumen' => 'required|string',
                'npwp_penjual' => 'required|string',
                'npwp_pembeli' => 'required|string',
                'buyer_id_type' => 'required|string',
                'negara_pembeli' => 'required|string',
                'nomor_dokumen_pembeli' => 'nullable|string',
                'nama_pembeli' => 'required|string',
                'alamat_pembeli' => 'required|string',
                'email_pembeli' => 'nullable|email',
                'id_tku_pembeli' => 'required|string',
                'uang_muka' => 'boolean',
                'pelunasan' => 'boolean',
                'nomor_faktur' => 'required|string|max:255|regex:/^[A-Z0-9.-]+$/',
            ]);

            $laporanFaktur->update([
                'tanggal_faktur' => $fakturData['tanggal_faktur'],
                'jenis_faktur' => $fakturData['jenis_faktur'],
                'kode_transaksi' => $fakturData['kode_transaksi'],
                'referensi' => $fakturData['referensi'],
                'alamat_penjual' => $fakturData['alamat_dokumen'],
                'id_tku_penjual' => $fakturData['id_tku_dokumen'],
                'npwp_penjual' => $fakturData['npwp_penjual'],
                'npwp_nik_pembeli' => $fakturData['npwp_pembeli'],
                'jenis_id_pembeli' => $fakturData['buyer_id_type'],
                'negara_pembeli' => $fakturData['negara_pembeli'],
                'nomor_dokumen_pembeli' => $fakturData['nomor_dokumen_pembeli'],
                'nama_pembeli' => $fakturData['nama_pembeli'],
                'alamat_pembeli' => $fakturData['alamat_pembeli'],
                'email_pembeli' => $fakturData['email_pembeli'],
                'id_tku_pembeli' => $fakturData['id_tku_pembeli'],
                'uang_muka' => $request->boolean('uang_muka'),
                'pelunasan' => $request->boolean('pelunasan'),
                'nomor_faktur' => $request->input('nomor_faktur'),
            ]);

            // Tangani pembaruan item detail (hapus yang lama dan buat ulang)
            $laporanFaktur->details()->delete();
            if ($request->has('items')) {
                foreach ($request->items as $index => $item) {
                    $detailData = [
                        'faktur_id' => $laporanFaktur->id,
                        'baris' => $index + 1,
                        'barang_jasa' => $item['jenis_barang_jasa'],
                        'kode_barang_jasa' => $item['kode_barang_jasa'],
                        'nama_barang_jasa' => $item['nama_barang_jasa'],
                        'nama_satuan_ukur' => $item['nama_satuan_ukur'],
                        'harga_satuan' => $item['harga_satuan'],
                        'jumlah_barang_jasa' => $item['jumlah_barang_jasa'],
                        'total_diskon' => $item['total_diskon'] ?? 0,
                        'dpp' => $item['dpp'] ?? 0,
                        'dpp_nilai_lain' => $item['dpp_nilai_lain'] ?? 0,
                        'tarif_ppn' => $item['tarif_ppn'] ?? 11.00,
                        'ppn' => $item['ppn'] ?? 0,
                        'tarif_ppnbm' => $item['tarif_ppnbm'] ?? 0,
                        'ppnbm' => $item['ppnbm'] ?? 0,
                    ];
                    DetailFaktur::create($detailData);
                }
            }

            return redirect()->route('laporan_faktur.index')
                           ->with('success', 'Faktur berhasil diperbarui');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faktur $laporanFaktur)
    {
        try {
            $laporanFaktur->details()->delete(); // Hapus detail item terkait
            $laporanFaktur->delete(); // Hapus record faktur

            return redirect()->route('laporan_faktur.index')
                           ->with('success', 'Faktur berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus faktur: ' . $e->getMessage());
        }
    }

    /**
     * Generate XML for e-Faktur
     */
    public function exportXML(Faktur $laporanFaktur)
    {
        $laporanFaktur->load('details');

        if ($laporanFaktur->details->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada detail barang/jasa untuk faktur ini.');
        }

        $xmlContent = $this->generateFakturXml($laporanFaktur);

        $filename = 'e-faktur-' . $laporanFaktur->id . '.xml';
        return Response::make($xmlContent, 200, [
            'Content-Type' => 'application/xml',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Preview XML for e-Faktur
     */
    public function previewXML(Faktur $laporanFaktur)
    {
        $laporanFaktur->load('details');

        if ($laporanFaktur->details->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada detail barang/jasa untuk faktur ini.');
        }

        $xmlContent = $this->generateFakturXml($laporanFaktur);

        return response($xmlContent)->header('Content-Type', 'text/xml');
    }

    /**
     * Helper untuk menghasilkan format XML e-Faktur dari model Faktur dan detailnya.
     */
    private function generateFakturXml(Faktur $faktur)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<Faktur>' . "\n"; // Elemen root untuk satu Faktur

        // Tambahkan informasi header Faktur
        $xml .= '  <ID_FAKTUR>' . $faktur->id . '</ID_FAKTUR>' . "\n";
        $xml .= '  <TANGGAL_FAKTUR>' . $faktur->tanggal_faktur->format('Y-m-d') . '</TANGGAL_FAKTUR>' . "\n";
        $xml .= '  <JENIS_FAKTUR>' . htmlspecialchars($faktur->jenis_faktur) . '</JENIS_FAKTUR>' . "\n";
        $xml .= '  <KODE_TRANSAKSI>' . htmlspecialchars($faktur->kode_transaksi) . '</KODE_TRANSAKSI>' . "\n";
        $xml .= '  <NOMOR_FAKTUR>' . htmlspecialchars($faktur->nomor_faktur) . '</NOMOR_FAKTUR>' . "\n";
        $xml .= '  <REFERENSI>' . htmlspecialchars($faktur->referensi) . '</REFERENSI>' . "\n";
        $xml .= '  <ALAMAT_PENJUAL>' . htmlspecialchars($faktur->alamat_penjual) . '</ALAMAT_PENJUAL>' . "\n";
        $xml .= '  <ID_TKU_PENJUAL>' . htmlspecialchars($faktur->id_tku_penjual) . '</ID_TKU_PENJUAL>' . "\n";
        $xml .= '  <NPWP_NIK_PEMBELI>' . htmlspecialchars($faktur->npwp_nik_pembeli) . '</NPWP_NIK_PEMBELI>' . "\n";
        $xml .= '  <JENIS_ID_PEMBELI>' . htmlspecialchars($faktur->jenis_id_pembeli) . '</JENIS_ID_PEMBELI>' . "\n";
        $xml .= '  <NEGARA_PEMBELI>' . htmlspecialchars($faktur->negara_pembeli) . '</NEGARA_PEMBELI>' . "\n";
        $xml .= '  <NOMOR_DOKUMEN_PEMBELI>' . htmlspecialchars($faktur->nomor_dokumen_pembeli) . '</NOMOR_DOKUMEN_PEMBELI>' . "\n";
        $xml .= '  <NAMA_PEMBELI>' . htmlspecialchars($faktur->nama_pembeli) . '</NAMA_PEMBELI>' . "\n";
        $xml .= '  <ALAMAT_PEMBELI>' . htmlspecialchars($faktur->alamat_pembeli) . '</ALAMAT_PEMBELI>' . "\n";
        $xml .= '  <EMAIL_PEMBELI>' . htmlspecialchars($faktur->email_pembeli) . '</EMAIL_PEMBELI>' . "\n";
        $xml .= '  <ID_TKU_PEMBELI>' . htmlspecialchars($faktur->id_tku_pembeli) . '</ID_TKU_PEMBELI>' . "\n";
        $xml .= '  <UANG_MUKA>' . ($faktur->uang_muka ? '1' : '0') . '</UANG_MUKA>' . "\n";
        $xml .= '  <PELUNASAN>' . ($faktur->pelunasan ? '1' : '0') . '</PELUNASAN>' . "\n";

        // Tambahkan detail item
        $xml .= '  <DETAIL_BARANG_JASA>' . "\n";
        foreach ($faktur->details as $item) {
            $xml .= '    <ITEM>' . "\n";
            $xml .= '      <BARIS>' . $item->baris . '</BARIS>' . "\n";
            $xml .= '      <JENIS_BARANG_JASA>' . $item->barang_jasa . '</JENIS_BARANG_JASA>' . "\n";
            $xml .= '      <KODE_BARANG_JASA>' . htmlspecialchars($item->kode_barang_jasa) . '</KODE_BARANG_JASA>' . "\n";
            $xml .= '      <NAMA_BARANG_JASA>' . htmlspecialchars($item->nama_barang_jasa) . '</NAMA_BARANG_JASA>' . "\n";
            $xml .= '      <NAMA_SATUAN_UKUR>' . htmlspecialchars($item->nama_satuan_ukur) . '</NAMA_SATUAN_UKUR>' . "\n";
            $xml .= '      <HARGA_SATUAN>' . number_format($item->harga_satuan, 2, '.', '') . '</HARGA_SATUAN>' . "\n";
            $xml .= '      <JUMLAH_BARANG_JASA>' . number_format($item->jumlah_barang_jasa, 2, '.', '') . '</JUMLAH_BARANG_JASA>' . "\n";
            $xml .= '      <TOTAL_DISKON>' . number_format($item->total_diskon, 2, '.', '') . '</TOTAL_DISKON>' . "\n";
            $xml .= '      <DPP>' . number_format($item->dpp, 2, '.', '') . '</DPP>' . "\n";
            $xml .= '      <DPP_NILAI_LAIN>' . number_format($item->dpp_nilai_lain, 2, '.', '') . '</DPP_NILAI_LAIN>' . "\n";
            $xml .= '      <TARIF_PPN>' . number_format($item->tarif_ppn, 2, '.', '') . '</TARIF_PPN>' . "\n";
            $xml .= '      <PPN>' . number_format($item->ppn, 2, '.', '') . '</PPN>' . "\n";
            $xml .= '      <TARIF_PPNBM>' . number_format($item->tarif_ppnbm, 2, '.', '') . '</TARIF_PPNBM>' . "\n";
            $xml .= '      <PPNBM>' . number_format($item->ppnbm, 2, '.', '') . '</PPNBM>' . "\n";
            $xml .= '    </ITEM>' . "\n";
        }
        $xml .= '  </DETAIL_BARANG_JASA>' . "\n";
        $xml .= '</Faktur>';

        return $xml;
    }
}