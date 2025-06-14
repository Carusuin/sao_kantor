@extends('dashboard_layout')

@section('title', 'Detail Laporan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-eye mr-2"></i>
                        Detail Laporan #{{ $laporan->id }}
                    </h3>
                    <div class="card-tools">
                        <div class="btn-group">
                            <a href="{{ route('laporan.edit', $laporan) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit mr-1"></i>
                                Edit
                            </a>
                            <a href="{{ route('laporan.export.xml', $laporan) }}" class="btn btn-success btn-sm">
                                <i class="fas fa-download mr-1"></i>
                                Download XML
                            </a>
                            <a href="{{ route('laporan.preview.xml', $laporan) }}" class="btn btn-info btn-sm" target="_blank">
                                <i class="fas fa-code mr-1"></i>
                                Preview XML
                            </a>
                            <a href="{{ route('laporan.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left mr-1"></i>
                                Kembali
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <!-- Main Information -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Informasi Utama</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="40%"><strong>TIN</strong></td>
                                            <td>: {{ $laporan->tin }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Periode Pajak</strong></td>
                                            <td>: {{ str_pad($laporan->tax_period_month, 2, '0', STR_PAD_LEFT) }}/{{ $laporan->tax_period_year }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Kode Transaksi</strong></td>
                                            <td>: {{ $laporan->trx_code }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tanggal Transaksi</strong></td>
                                            <td>: {{ $laporan->transaction_date->format('d-m-Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Serial No</strong></td>
                                            <td>: {{ $laporan->serial_no }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Good Service Opt</strong></td>
                                            <td>: {{ $laporan->good_service_opt }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Buyer Information -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">Informasi Pembeli</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="40%"><strong>Nama Pembeli</strong></td>
                                            <td>: {{ $laporan->buyer_name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tipe ID Pembeli</strong></td>
                                            <td>: {{ $laporan->buyer_id_opt }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Nomor ID Pembeli</strong></td>
                                            <td>: {{ $laporan->buyer_id_number }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>STLG</strong></td>
                                            <td>: {{ $laporan->stlg }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Info</strong></td>
                                            <td>: {{ $laporan->info }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Information -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="mb-0">Informasi Keuangan</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-primary">
                                                    <i class="fas fa-money-bill-wave"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Tax Base Selling Price</span>
                                                    <span class="info-box-number">
                                                        Rp {{ number_format($laporan->tax_base_selling_price, 0, ',', '.') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-success">
                                                    <i class="fas fa-calculator"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Other Tax Selling Price</span>
                                                    <span class="info-box-number">
                                                        Rp {{ number_format($laporan->other_tax_selling_price, 0, ',', '.') }}
                                                    </span>
                                                    <small class="text-muted">Tax Base × 11 ÷ 12</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-warning">
                                                    <i class="fas fa-percentage"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">VAT</span>
                                                    <span class="info-box-number">
                                                        Rp {{ number_format($laporan->vat, 0, ',', '.') }}
                                                    </span>
                                                    <small class="text-muted">Tax Base × 12%</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- XML Preview -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Preview XML</h5>
                                    <button type="button" class="btn btn-sm btn-light" onclick="copyXML()">
                                        <i class="fas fa-copy mr-1"></i>
                                        Copy XML
                                    </button>
                                </div>
                                <div class="card-body p-0">
                                    <pre class="bg-light p-3 mb-0" id="xmlContent"><code>{{ $laporan->xml_content }}</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Metadata -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="mb-0">Metadata</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Dibuat pada:</strong> {{ $laporan->created_at->format('d F Y, H:i:s') }}</p>
                                            <p><strong>Diubah pada:</strong> {{ $laporan->updated_at->format('d F Y, H:i:s') }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>ID Laporan:</strong> #{{ $laporan->id }}</p>
                                            <p><strong>Status:</strong> <span class="badge badge-success">Aktif</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="{{ route('laporan.edit', $laporan) }}" class="btn btn-warning btn-block">
                                <i class="fas fa-edit mr-2"></i>
                                Edit Laporan
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('laporan.export.xml', $laporan) }}" class="btn btn-success btn-block">
                                <i class="fas fa-download mr-2"></i>
                                Download XML
                            </a>
                        </div>
                        <div class="col-md-3">
                            <form action="{{ route('laporan.destroy', $laporan) }}" method="POST" class="d-inline w-100" onsubmit="return confirm('Yakin ingin menghapus laporan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-block">
                                    <i class="fas fa-trash mr-2"></i>
                                    Hapus Laporan
                                </button>
                            </form>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('laporan.index') }}" class="btn btn-secondary btn-block">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Kembali ke Daftar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .info-box {
        display: block;
        min-height: 90px;
        background: #fff;
        width: 100%;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
        border-radius: 2px;
        margin-bottom: 15px;
    }

    .info-box-icon {
        border-top-left-radius: 2px;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 2px;
        display: block;
        float: left;
        height: 90px;
        width: 90px;
        text-align: center;
        font-size: 45px;
        line-height: 90px;
        background: rgba(0,0,0,0.2);
    }

    .info-box-content {
        padding: 5px 10px;
        margin-left: 90px;
    }

    .info-box-text {
        text-transform: uppercase;
        font-weight: bold;
        font-size: 13px;
    }

    .info-box-number {
        display: block;
        font-weight: bold;
        font-size: 18px;
    }

    pre {
        max-height: 400px;
        overflow-y: auto;
        font-size: 12px;
        line-height: 1.4;
    }

    .table-borderless td {
        border: none !important;
        padding: 8px 0;
    }
</style>
@endpush

@push('scripts')
<script>
function copyXML() {
    const xmlContent = document.getElementById('xmlContent').textContent;
    
    if (navigator.clipboard) {
        navigator.clipboard.writeText(xmlContent).then(function() {
            alert('XML berhasil disalin ke clipboard!');
        }).catch(function(err) {
            console.error('Gagal menyalin: ', err);
            fallbackCopyTextToClipboard(xmlContent);
        });
    } else {
        fallbackCopyTextToClipboard(xmlContent);
    }
}

function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.position = "fixed";
    
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            alert('XML berhasil disalin ke clipboard!');
        } else {
            alert('Gagal menyalin XML');
        }
    } catch (err) {
        alert('Gagal menyalin XML');
    }
    
    document.body.removeChild(textArea);
}
</script>
@endpush