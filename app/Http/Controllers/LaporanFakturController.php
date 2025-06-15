<?php

namespace App\Http\Controllers;

use App\Models\BarangJasa;
use App\Models\LaporanFaktur;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class LaporanFakturController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = LaporanFaktur::query();

        // Filter berdasarkan dokumen_id
        if ($request->has('dokumen_id') && $request->dokumen_id) {
            $query->byDokumen($request->dokumen_id);
        }

        // Filter berdasarkan jenis barang/jasa
        if ($request->has('jenis') && in_array($request->jenis, ['B', 'J'])) {
            $query->where('jenis_barang_jasa', $request->jenis);
        }

        // Filter berdasarkan nama barang/jasa
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_barang_jasa', 'like', '%' . $request->search . '%')
                  ->orWhere('kode_barang_jasa', 'like', '%' . $request->search . '%');
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'baris');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $laporans = $query->paginate($perPage);

        return view('laporanfaktur.index', compact('laporans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Pass necessary data to the view
        $taxPeriodMonth = now()->month;
        $taxPeriodYear = now()->year;
        $transactionDate = now()->format('Y-m-d');

        return view('laporanfaktur.create', compact('taxPeriodMonth', 'taxPeriodYear', 'transactionDate'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate faktur data
            $fakturData = $request->validate([
                'tanggal_faktur' => 'required|date',
                'jenis_faktur' => 'required|string',
                'kode_transaksi' => 'required|string',
                'referensi' => 'nullable|string',
                'alamat_dokumen' => 'required|string',
                'id_tku_dokumen' => 'required|string',
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
            ]);

            // Create faktur record
            $faktur = \App\Models\Faktur::create([
                'tanggal_faktur' => $fakturData['tanggal_faktur'],
                'jenis_faktur' => $fakturData['jenis_faktur'],
                'kode_transaksi' => $fakturData['kode_transaksi'],
                'referensi' => $fakturData['referensi'],
                'alamat_penjual' => $fakturData['alamat_dokumen'],
                'id_tku_penjual' => $fakturData['id_tku_dokumen'],
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
            ]);

            // Validate and store detail items
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

                    \App\Models\DetailFaktur::create($detailData);
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
    public function show(LaporanFaktur $laporanFaktur): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $laporanFaktur,
            'calculated' => [
                'total' => $laporanFaktur->calculateTotal(),
                'formatted_harga' => $laporanFaktur->formatted_harga_satuan,
                'formatted_dpp' => $laporanFaktur->formatted_dpp,
                'formatted_ppn' => $laporanFaktur->formatted_ppn,
                'jenis_text' => $laporanFaktur->jenis_barang_jasa_text
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LaporanFaktur $laporanFaktur): JsonResponse
    {
        $validator = $this->validateBarangJasa($request, $laporanFaktur->id);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $laporanFaktur->update($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => $laporanFaktur,
            'message' => 'Data barang/jasa berhasil diperbarui'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LaporanFaktur $laporanFaktur): JsonResponse
    {
        $laporanFaktur->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data barang/jasa berhasil dihapus'
        ]);
    }

    /**
     * Bulk delete barang/jasa by dokumen_id
     */
    public function bulkDeleteByDokumen(Request $request): JsonResponse
    {
        $request->validate([
            'dokumen_id' => 'required|integer'
        ]);

        $deleted = LaporanFaktur::where('dokumen_id', $request->dokumen_id)->delete();

        return response()->json([
            'status' => 'success',
            'message' => "Berhasil menghapus {$deleted} item barang/jasa",
            'deleted_count' => $deleted
        ]);
    }

    /**
     * Get summary by dokumen_id
     */
    public function getSummaryByDokumen(Request $request): JsonResponse
    {
        $request->validate([
            'dokumen_id' => 'required|integer'
        ]);

        $items = LaporanFaktur::byDokumen($request->dokumen_id)->get();

        $summary = [
            'total_items' => $items->count(),
            'total_barang' => $items->where('jenis_barang_jasa', 'B')->count(),
            'total_jasa' => $items->where('jenis_barang_jasa', 'J')->count(),
            'total_dpp' => $items->sum('dpp'),
            'total_ppn' => $items->sum('ppn'),
            'total_ppnbm' => $items->sum('ppnbm'),
            'grand_total' => $items->sum(function ($item) {
                return $item->calculateTotal();
            })
        ];

        return response()->json([
            'status' => 'success',
            'data' => $summary
        ]);
    }

    /**
     * Generate XML for e-Faktur
     */
    public function generateXml(Request $request): JsonResponse
    {
        $request->validate([
            'dokumen_id' => 'required|integer'
        ]);

        $items = LaporanFaktur::byDokumen($request->dokumen_id)
                          ->orderBy('baris')
                          ->get();

        if ($items->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada data barang/jasa untuk dokumen ini'
            ], 404);
        }

        $xml = $this->generateEFakturXml($items);

        return response()->json([
            'status' => 'success',
            'data' => [
                'xml_content' => $xml,
                'total_items' => $items->count()
            ],
            'message' => 'XML e-Faktur berhasil dibuat'
        ]);
    }

    /**
     * Validation rules
     */
    private function validateBarangJasa(Request $request, $id = null)
    {
        return Validator::make($request->all(), [
            'baris' => 'required|integer|min:1',
            'jenis_barang_jasa' => 'required|in:B,J',
            'kode_barang_jasa' => 'required|string|max:50',
            'nama_barang_jasa' => 'required|string|max:255',
            'nama_satuan_ukur' => 'required|string|max:50',
            'harga_satuan' => 'required|numeric|min:0',
            'jumlah_barang_jasa' => 'required|numeric|min:0',
            'total_diskon' => 'nullable|numeric|min:0',
            'dpp' => 'nullable|numeric|min:0',
            'dpp_nilai_lain' => 'nullable|numeric|min:0',
            'tarif_ppn' => 'required|numeric|min:0|max:100',
            'ppn' => 'nullable|numeric|min:0',
            'tarif_ppnbm' => 'nullable|numeric|min:0|max:100',
            'ppnbm' => 'nullable|numeric|min:0',
            'dokumen_id' => 'nullable|integer'
        ]);
    }

    /**
     * Generate e-Faktur XML format
     */
    private function generateEFakturXml($items)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<BARANG_JASA>' . "\n";

        foreach ($items as $item) {
            $xml .= '  <ITEM>' . "\n";
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
        }

        $xml .= '</BARANG_JASA>';

        return $xml;
    }
}