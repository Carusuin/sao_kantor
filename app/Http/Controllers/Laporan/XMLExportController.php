<?php
// app/Http/Controllers/XmlExportController.php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Services\XmlExportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class XmlExportController extends Controller
{
    protected $xmlExportService;
    
    public function __construct(XmlExportService $xmlExportService)
    {
        $this->xmlExportService = $xmlExportService;
    }
    
    /**
     * Show export options page
     */
    public function index()
    {
        $periods = Laporan::selectRaw('tax_period_year, tax_period_month, COUNT(*) as count')
            ->groupBy('tax_period_year', 'tax_period_month')
            ->orderBy('tax_period_year', 'desc')
            ->orderBy('tax_period_month', 'desc')
            ->get();
            
        $totalRecords = Laporan::count();
        
        return view('export.xml.index', compact('periods', 'totalRecords'));
    }
    
    /**
     * Export all records to XML
     */
    public function exportAll(Request $request)
    {
        try {
            $laporans = Laporan::with([])
                ->orderBy('tax_period_year', 'desc')
                ->orderBy('tax_period_month', 'desc')
                ->orderBy('transaction_date', 'desc')
                ->get();
            
            if ($laporans->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada data laporan untuk diekspor.');
            }
            
            $options = [
                'include_metadata' => $request->boolean('include_metadata', true),
                'group_by_period' => $request->boolean('group_by_period', false)
            ];
            
            $xmlContent = $this->xmlExportService->exportWithOptions($laporans, $options);
            $filename = $this->xmlExportService->generateFilename();
            
            // Log export activity
            $this->logExportActivity('all', $laporans->count());
            
            return $this->downloadXml($xmlContent, $filename);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor data: ' . $e->getMessage());
        }
    }
    
    /**
     * Export by specific period
     */
    public function exportByPeriod(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'period_month' => 'required|integer|min:1|max:12',
            'period_year' => 'required|integer|min:2020|max:' . (date('Y') + 1)
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->with('error', 'Data periode tidak valid.');
        }
        
        try {
            $month = $request->period_month;
            $year = $request->period_year;
            
            $xmlContent = $this->xmlExportService->exportByPeriod($month, $year);
            $filename = $this->xmlExportService->generateFilename([
                'by_period' => true,
                'period_month' => $month,
                'period_year' => $year
            ]);
            
            // Count records for logging
            $count = Laporan::where('tax_period_month', $month)
                ->where('tax_period_year', $year)
                ->count();
            
            if ($count == 0) {
                return redirect()->back()->with('warning', 
                    'Tidak ada data untuk periode ' . DateTime::createFromFormat('!m', $month)->format('F') . ' ' . $year);
            }
            
            $this->logExportActivity('period', $count, [
                'month' => $month, 
                'year' => $year
            ]);
            
            return $this->downloadXml($xmlContent, $filename);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor data periode: ' . $e->getMessage());
        }
    }
    
    /**
     * Export single record
     */
    public function exportSingle(Laporan $laporan)
    {
        try {
            $xmlContent = $this->xmlExportService->exportSingleToXml($laporan);
            $filename = $this->xmlExportService->generateFilename(['single' => true]);
            
            $this->logExportActivity('single', 1, ['laporan_id' => $laporan->id]);
            
            return $this->downloadXml($xmlContent, $filename);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor laporan: ' . $e->getMessage());
        }
    }
    
    /**
     * Export with custom date range
     */
    public function exportByDateRange(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->with('error', 'Rentang tanggal tidak valid.');
        }
        
        try {
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            
            $laporans = Laporan::whereBetween('transaction_date', [$startDate, $endDate])
                ->orderBy('transaction_date', 'desc')
                ->get();
            
            if ($laporans->isEmpty()) {
                return redirect()->back()->with('warning', 
                    'Tidak ada data dalam rentang tanggal ' . $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y'));
            }
            
            $options = [
                'date_range' => true,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'include_metadata' => $request->boolean('include_metadata', true)
            ];
            
            $xmlContent = $this->xmlExportService->exportWithOptions($laporans, $options);
            $filename = 'tax_report_' . $startDate->format('Ymd') . '_to_' . $endDate->format('Ymd') . '_' . Carbon::now()->format('His') . '.xml';
            
            $this->logExportActivity('date_range', $laporans->count(), [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d')
            ]);
            
            return $this->downloadXml($xmlContent, $filename);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor data berdasarkan tanggal: ' . $e->getMessage());
        }
    }
    
    /**
     * Preview XML content before download
     */
    public function preview(Request $request)
    {
        try {
            $type = $request->get('type', 'all');
            $limit = 5; // Preview only first 5 records
            
            switch ($type) {
                case 'period':
                    $month = $request->get('month');
                    $year = $request->get('year');
                    $laporans = Laporan::where('tax_period_month', $month)
                        ->where('tax_period_year', $year)
                        ->limit($limit)
                        ->get();
                    break;
                    
                case 'date_range':
                    $startDate = $request->get('start_date');
                    $endDate = $request->get('end_date');
                    $laporans = Laporan::whereBetween('transaction_date', [$startDate, $endDate])
                        ->limit($limit)
                        ->get();
                    break;
                    
                default:
                    $laporans = Laporan::limit($limit)->get();
                    break;
            }
            
            if ($laporans->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data untuk preview.'
                ]);
            }
            
            $xmlContent = $this->xmlExportService->exportWithOptions($laporans, ['preview' => true]);
            
            return response()->json([
                'success' => true,
                'xml_content' => $xmlContent,
                'total_preview' => $laporans->count(),
                'note' => 'Menampilkan maksimal ' . $limit . ' record untuk preview'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat preview: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Validate XML content
     */
    public function validate(Request $request)
    {
        try {
            $xmlContent = $request->get('xml_content');
            
            if (empty($xmlContent)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Konten XML tidak boleh kosong.'
                ]);
            }
            
            $validation = $this->xmlExportService->validateXml($xmlContent);
            
            return response()->json([
                'success' => $validation['valid'],
                'valid' => $validation['valid'],
                'errors' => $validation['errors'],
                'message' => $validation['valid'] ? 'XML valid!' : 'XML tidak valid.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error validasi: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get export statistics
     */
    public function statistics()
    {
        try {
            $stats = [
                'total_records' => Laporan::count(),
                'records_by_year' => Laporan::selectRaw('tax_period_year, COUNT(*) as count')
                    ->groupBy('tax_period_year')
                    ->orderBy('tax_period_year', 'desc')
                    ->get(),
                'records_by_month' => Laporan::selectRaw('tax_period_month, COUNT(*) as count')
                    ->groupBy('tax_period_month')
                    ->orderBy('tax_period_month')
                    ->get(),
                'latest_record' => Laporan::latest('created_at')->first(),
                'oldest_record' => Laporan::oldest('created_at')->first(),
                'total_tax_amount' => Laporan::sum('vat'),
                'total_base_amount' => Laporan::sum('tax_base_selling_price')
            ];
            
            return response()->json([
                'success' => true,
                'statistics' => $stats
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Download XML response helper
     */
    private function downloadXml(string $xmlContent, string $filename): Response
    {
        return response($xmlContent, 200, [
            'Content-Type' => 'application/xml',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }
    
    /**
     * Log export activity
     */
    private function logExportActivity(string $type, int $recordCount, array $metadata = []): void
    {
        // You can implement logging to database or file here
        \Log::info('XML Export Activity', [
            'type' => $type,
            'record_count' => $recordCount,
            'user_id' => auth()->id(),
            'metadata' => $metadata,
            'timestamp' => Carbon::now()
        ]);
    }
}