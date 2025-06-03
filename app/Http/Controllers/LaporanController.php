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
        $previousMonth = now()->subMonth();
        $taxPeriodMonth = $previousMonth->month;
        $taxPeriodYear = $previousMonth->year;
        $transactionDate = \App\Models\Laporan::getLastDayOfPreviousMonth(); // Use the static method from the Laporan model

        return view('laporan.create', compact('taxPeriodMonth', 'taxPeriodYear', 'transactionDate'));
    }

    /**
     * Store new laporan
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tin' => 'required|string|size:16',
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
        $laporan->tax_period_month = $request->tax_period_month;
        $laporan->tax_period_year = $request->tax_period_year;
        $laporan->trx_code = $request->trx_code;
        $laporan->buyer_name = $request->buyer_name;
        $laporan->buyer_id_opt = $request->buyer_id_opt;
        $laporan->buyer_id_number = $request->buyer_id_number;
        $laporan->good_service_opt = $request->good_service_opt;
        $laporan->serial_no = $request->serial_no;
        $laporan->transaction_date = $request->transaction_date;
        $laporan->stlg = $request->stlg;
        $laporan->info = $request->info;
        
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
        $tin = $laporan->tin;
        $month = str_pad($laporan->tax_period_month, 2, '0', STR_PAD_LEFT); // Format month with leading zero
        $year = $laporan->tax_period_year;
        $filename = $tin . '_DIGUNGGUNG_' . $month . '_' . $year . '.xml';
        
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