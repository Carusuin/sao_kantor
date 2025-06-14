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
    public function index(Request $request): JsonResponse
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
        $barangJasa = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $barangJasa,
            'message' => 'Data barang/jasa berhasil diambil'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = $this->validateBarangJasa($request);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $barangJasa = LaporanFaktur::create($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => $barangJasa,
            'message' => 'Data barang/jasa berhasil disimpan'
        ], 201);
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