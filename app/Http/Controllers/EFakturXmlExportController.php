<?php

namespace App\Http\Controllers;

use App\Models\Faktur;
use App\Services\EFakturXmlExportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class EFakturXmlExportController extends Controller
{
    protected $xmlExportService;
    
    public function __construct(EFakturXmlExportService $xmlExportService)
    {
        $this->xmlExportService = $xmlExportService;
    }
    
    /**
     * Export all e-faktur records to XML
     */
    public function exportAll(Request $request)
    {
        try {
            $fakturs = Faktur::with('details')
                ->orderBy('tanggal_faktur', 'desc')
                ->get();
            
            if ($fakturs->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada data e-faktur untuk diekspor.');
            }
            
            $xmlContent = $this->xmlExportService->exportToXml($fakturs);
            $filename = $this->xmlExportService->generateFilename();
            
            $this->logExportActivity('all', $fakturs->count());
            
            return $this->downloadXml($xmlContent, $filename);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor data: ' . $e->getMessage());
        }
    }
    
    /**
     * Export single e-faktur record
     */
    public function exportSingle(Faktur $faktur)
    {
        try {
            $faktur->load('details');
            $xmlContent = $this->xmlExportService->exportToXml(collect([$faktur]), ['single' => true]);
            $filename = $this->xmlExportService->generateFilename(['single' => true]);
            
            $this->logExportActivity('single', 1, ['faktur_id' => $faktur->id]);
            
            return $this->downloadXml($xmlContent, $filename);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor e-faktur: ' . $e->getMessage());
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
            
            $fakturs = Faktur::with('details')
                ->whereBetween('tanggal_faktur', [$startDate, $endDate])
                ->orderBy('tanggal_faktur', 'desc')
                ->get();
            
            if ($fakturs->isEmpty()) {
                return redirect()->back()->with('warning', 
                    'Tidak ada data dalam rentang tanggal ' . $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y'));
            }
            
            $xmlContent = $this->xmlExportService->exportToXml($fakturs);
            $filename = 'efaktur_report_' . $startDate->format('Ymd') . '_to_' . $endDate->format('Ymd') . '_' . Carbon::now()->format('His') . '.xml';
            
            $this->logExportActivity('date_range', $fakturs->count(), [
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
            $limit = 5; // Preview only first 5 records
            $fakturs = Faktur::with('details')
                ->limit($limit)
                ->get();
            
            if ($fakturs->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data untuk preview.'
                ]);
            }
            
            $xmlContent = $this->xmlExportService->exportToXml($fakturs);
            
            return response()->json([
                'success' => true,
                'xml_content' => $xmlContent,
                'total_preview' => $fakturs->count(),
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
        \Log::info('E-Faktur XML Export Activity', [
            'type' => $type,
            'record_count' => $recordCount,
            'user_id' => auth()->id(),
            'metadata' => $metadata,
            'timestamp' => Carbon::now()
        ]);
    }
} 