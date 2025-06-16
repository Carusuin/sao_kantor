@extends('dashboard_layout')

@section('title', 'Detail Faktur Pajak Keluaran')
@section('page-title', 'Detail Faktur Pajak Keluaran')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Detail Faktur Pajak Keluaran
                    </h4>
                </div>
                
                <div class="card-body">
                    @if($laporanFaktur)
                        <!-- Dokumen Transaksi Section -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Dokumen Transaksi</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <p class="form-label">Uang Muka</p>
                                        <p class="form-control-static">{{ $laporanFaktur->uang_muka ? 'Ya' : 'Tidak' }}</p>
                                    </div>
                                    <div class="col-md-2">
                                        <p class="form-label">Pelunasan</p>
                                        <p class="form-control-static">{{ $laporanFaktur->pelunasan ? 'Ya' : 'Tidak' }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="form-label">Nomor Faktur</p>
                                        <p class="form-control-static">{{ $laporanFaktur->nomor_faktur }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="form-label">Kode Transaksi</p>
                                        <p class="form-control-static">{{ $laporanFaktur->kode_transaksi }}</p>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <p class="form-label">Tanggal Faktur</p>
                                        <p class="form-control-static">{{ $laporanFaktur->tanggal_faktur->format('d-m-Y') }}</p>
                                    </div>
                                    <div class="col-md-3">
                                        <p class="form-label">Jenis Faktur</p>
                                        <p class="form-control-static">{{ $laporanFaktur->jenis_faktur }}</p>
                                    </div>
                                    {{-- Masa Pajak and Tahun Pajak are not in Faktur fillable, omitting. --}}
                                    <div class="col-md-6">
                                        <p class="form-label">Referensi</p>
                                        <p class="form-control-static">{{ $laporanFaktur->referensi ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="form-label">Alamat Dokumen</p>
                                        <p class="form-control-static">{{ $laporanFaktur->alamat_penjual }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="form-label">IDTKU Dokumen</p>
                                        <p class="form-control-static">{{ $laporanFaktur->id_tku_penjual }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Informasi Pembeli Section -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Informasi Pembeli</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <p class="form-label">NPWP</p>
                                        <p class="form-control-static">{{ $laporanFaktur->npwp_nik_pembeli }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="form-label">ID Tipe</p>
                                        <p class="form-control-static">{{ $laporanFaktur->jenis_id_pembeli }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="form-label">Negara</p>
                                        <p class="form-control-static">{{ $laporanFaktur->negara_pembeli }}</p>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <p class="form-label">Nomor Dokumen</p>
                                        <p class="form-control-static">{{ $laporanFaktur->nomor_dokumen_pembeli ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="form-label">Nama</p>
                                        <p class="form-control-static">{{ $laporanFaktur->nama_pembeli }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="form-label">Alamat</p>
                                        <p class="form-control-static">{{ $laporanFaktur->alamat_pembeli }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <p class="form-label">IDTKU</p>
                                        <p class="form-control-static">{{ $laporanFaktur->id_tku_pembeli }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="form-label">Email</p>
                                        <p class="form-control-static">{{ $laporanFaktur->email_pembeli ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Detail Transaksi Section -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-list-alt me-2"></i>Detail Transaksi</h5>
                            </div>
                            <div class="card-body">
                                @if($laporanFaktur->details->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="transactionTable">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>Baris</th>
                                                    <th>Tipe</th>
                                                    <th>Nama</th>
                                                    <th>Kode</th>
                                                    <th>Kuantitas</th>
                                                    <th>Satuan</th>
                                                    <th>Harga Satuan</th>
                                                    <th>Total Harga</th>
                                                    <th>Potongan Harga</th>
                                                    <th>Tarif PPN</th>
                                                    <th>DPP</th>
                                                    <th>PPN</th>
                                                    <th>DPP Nilai Lain/DPP</th>
                                                    <th>PPnBM</th>
                                                    <th>Tarif PPnBM</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $totalHargaSum = 0;
                                                    $potonganHargaSum = 0;
                                                    $dppSum = 0;
                                                    $ppnSum = 0;
                                                    $dppNilaiLainSum = 0;
                                                    $ppnbmSum = 0;
                                                @endphp
                                                @foreach($laporanFaktur->details as $detail)
                                                    <tr>
                                                        <td>{{ $detail->baris }}</td>
                                                        <td>{{ $detail->barang_jasa === 'B' ? 'Barang' : 'Jasa' }}</td>
                                                        <td>{{ $detail->nama_barang_jasa }}</td>
                                                        <td>{{ $detail->kode_barang_jasa }}</td>
                                                        <td>{{ number_format($detail->jumlah_barang_jasa, 2, ',', '.') }}</td>
                                                        <td>{{ $detail->nama_satuan_ukur }}</td>
                                                        <td>{{ number_format($detail->harga_satuan, 2, ',', '.') }}</td>
                                                        <td>{{ number_format(($detail->jumlah_barang_jasa * $detail->harga_satuan) - $detail->total_diskon, 2, ',', '.') }}</td>
                                                        <td>{{ number_format($detail->total_diskon, 2, ',', '.') }}</td>
                                                        <td>{{ number_format($detail->tarif_ppn, 2, ',', '.') }}%</td>
                                                        <td>{{ number_format($detail->dpp, 2, ',', '.') }}</td>
                                                        <td>{{ number_format($detail->ppn, 2, ',', '.') }}</td>
                                                        <td>{{ number_format($detail->dpp_nilai_lain, 2, ',', '.') }}</td>
                                                        <td>{{ number_format($detail->ppnbm, 2, ',', '.') }}</td>
                                                        <td>{{ number_format($detail->tarif_ppnbm, 2, ',', '.') }}%</td>
                                                    </tr>
                                                    @php
                                                        $totalHargaSum += ($detail->jumlah_barang_jasa * $detail->harga_satuan) - $detail->total_diskon;
                                                        $potonganHargaSum += $detail->total_diskon;
                                                        $dppSum += $detail->dpp;
                                                        $ppnSum += $detail->ppn;
                                                        $dppNilaiLainSum += $detail->dpp_nilai_lain;
                                                        $ppnbmSum += $detail->ppnbm;
                                                    @endphp
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="7" class="text-end"><strong>JUMLAH</strong></td>
                                                    <td class="text-end">{{ number_format($totalHargaSum, 2, ',', '.') }}</td>
                                                    <td class="text-end">{{ number_format($potonganHargaSum, 2, ',', '.') }}</td>
                                                    <td></td>
                                                    <td class="text-end">{{ number_format($dppSum, 2, ',', '.') }}</td>
                                                    <td class="text-end">{{ number_format($ppnSum, 2, ',', '.') }}</td>
                                                    <td class="text-end">{{ number_format($dppNilaiLainSum, 2, ',', '.') }}</td>
                                                    <td class="text-end">{{ number_format($ppnbmSum, 2, ',', '.') }}</td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                @else
                                    <p>Tidak ada detail barang/jasa untuk faktur ini.</p>
                                @endif
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <a href="{{ route('laporan_faktur.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left me-2"></i>Kembali
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-danger">
                            Faktur tidak ditemukan.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card {
    border: none;
    border-radius: 10px;
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
}

.form-control-static {
    display: block;
    padding-top: calc(0.375rem + 1px);
    padding-bottom: calc(0.375rem + 1px);
    margin-bottom: 0;
    font-size: 1rem;
    line-height: 1.5;
    color: #212529; /* Match default input text color */
}

.btn {
    border-radius: 8px;
    font-weight: 500;
    padding: 8px 16px;
}

.alert-info {
    background-color: #d1ecf1;
    border-color: #bee5eb;
    color: #0c5460;
}

/* Specific styles for the table headers to match the image's "sticky" or highlighted feel */
#transactionTable thead th {
    background-color: #f0f0f0; /* Light gray background */
    border-bottom: 2px solid #dee2e6;
    vertical-align: middle;
    white-space: nowrap; /* Prevent wrapping */
    padding: 0.75rem;
}

/* Adjusting font size for table inputs for a compact look */
#transactionTable .form-control-sm,
#transactionTable .form-select-sm {
    font-size: 0.875rem; /* Smaller font for inputs */
    padding: 0.25rem 0.5rem; /* Smaller padding */
}

.table-responsive {
    overflow-x: auto; /* Ensure horizontal scrolling for wide tables */
}
</style>
@endpush 