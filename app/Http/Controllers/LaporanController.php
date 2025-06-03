<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class LaporanController extends Controller
{
    /**
     * Display laporan index page
     */
    public function index()
    {
        $laporans = Laporan::orderBy('created_at', 'desc')->paginate(10);
        return view('laporan.index', compact('laporans'));
    }

    /**
     * Show form for creating new laporan
     */
    public function create()
    {
        return view('laporan.create');
    }

    /**
     * Store new laporan
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tin' => 'required|string|max:20',
            'tax_base_selling_price' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $laporan = new Laporan();
        
        // Set fixed values
        $laporan->tin = $request->tin;
        $laporan->tax_period_month = now()->month;
        $laporan->tax_period_year = now()->year;
        $laporan->trx_code = 'Normal';
        $laporan->buyer_name = '-';
        $laporan->buyer_id_opt = 'NIK';
        $laporan->buyer_id_number = '0000000000000000'; // 16 digits of zero
        $laporan->good_service_opt = 'A';
        $laporan->serial_no = '-';
        $laporan->transaction_date = Laporan::getLastDayOfPreviousMonth();
        $laporan->stlg = '0';
        $laporan->info = 'ok';
        
        // Set input values
        $laporan->tax_base_selling_price = $request->tax_base_selling_price;
        
        // Calculate derived values
        $laporan->other_tax_selling_price = $laporan->calculateOtherTaxSellingPrice($request->tax_base_selling_price);
        $laporan->vat = $laporan->calculateVAT($request->tax_base_selling_price);
        
        // Generate XML content
        $laporan->xml_content = $laporan->generateXMLContent();
        
        $laporan->save();

        return redirect()->route('laporan.index')
            ->with('success', 'Laporan berhasil dibuat!');
    }

    /**
     * Show specific laporan
     */
    public function show(Laporan $laporan)
    {
        return view('laporan.show', compact('laporan'));
    }

    /**
     * Show form for editing laporan
     */
    public function edit(Laporan $laporan)
    {
        return view('laporan.edit', compact('laporan'));
    }

    /**
     * Update laporan
     */
    public function update(Request $request, Laporan $laporan)
    {
        $validator = Validator::make($request->all(), [
            'tin' => 'required|string|max:20',
            'tax_base_selling_price' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update values
        $laporan->tin = $request->tin;
        $laporan->tax_base_selling_price = $request->tax_base_selling_price;
        
        // Recalculate derived values
        $laporan->other_tax_selling_price = $laporan->calculateOtherTaxSellingPrice($request->tax_base_selling_price);
        $laporan->vat = $laporan->calculateVAT($request->tax_base_selling_price);
        
        // Regenerate XML content
        $laporan->xml_content = $laporan->generateXMLContent();
        
        $laporan->save();

        return redirect()->route('laporan.index')
            ->with('success', 'Laporan berhasil diupdate!');
    }

    /**
     * Delete laporan
     */
    public function destroy(Laporan $laporan)
    {
        $laporan->delete();
        
        return redirect()->route('laporan.index')
            ->with('success', 'Laporan berhasil dihapus!');
    }

    /**
     * Export XML file
     */
    public function exportXML(Laporan $laporan)
    {
        $filename = 'laporan_' . $laporan->id . '_' . date('Y-m-d') . '.xml';
        
        return Response::make($laporan->xml_content, 200, [
            'Content-Type' => 'application/xml',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    /**
     * Preview XML content
     */
    public function previewXML(Laporan $laporan)
    {
        return response($laporan->xml_content)
            ->header('Content-Type', 'text/xml');
    }

    /**
     * Generate laporan with AJAX
     */
    public function generateLaporan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tin' => 'required|string|max:20',
            'tax_base_selling_price' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }

        $laporan = new Laporan();
        $laporan->tin = $request->tin;
        $laporan->tax_base_selling_price = $request->tax_base_selling_price;
        
        $otherTaxSellingPrice = $laporan->calculateOtherTaxSellingPrice($request->tax_base_selling_price);
        $vat = $laporan->calculateVAT($request->tax_base_selling_price);

        return response()->json([
            'success' => true,
            'data' => [
                'other_tax_selling_price' => number_format($otherTaxSellingPrice, 0, ',', '.'),
                'vat' => number_format($vat, 0, ',', '.'),
                'transaction_date' => Laporan::getLastDayOfPreviousMonth(),
                'tax_period_month' => now()->month,
                'tax_period_year' => now()->year
            ]
        ]);
    }
}