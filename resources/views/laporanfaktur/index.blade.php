@extends('dashboard_layout')

@section('title', 'Laporan E-Faktur')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-file-alt me-2"></i>
                        Daftar Laporan E-Faktur
                    </h3>
                    <div class="btn-group">
                        <a href="{{ route('laporan_faktur.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>
                            Tambah Laporan E-Faktur
                        </a>
                        <a href="{{ route('laporan_faktur.create_header') }}" class="btn btn-warning">
                            <i class="fas fa-plus me-1"></i>
                            Tambah Header Faktur Saja
                        </a>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exportModal">
                            <i class="fas fa-file-export me-1"></i>
                            Export XML
                        </button>
                    </div>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($fakturs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Tanggal Faktur</th>
                                        <th>Jenis Faktur</th>
                                        <th>Kode Transaksi</th>
                                        <th>NPWP/NIK Pembeli</th>
                                        <th>Nama Pembeli</th>
                                        <th>Nomor Faktur</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($fakturs as $faktur)
                                        <tr>
                                            <td>{{ $faktur->id }}</td>
                                            <td>{{ $faktur->tanggal_faktur->format('d-m-Y') }}</td>
                                            <td>{{ $faktur->jenis_faktur }}</td>
                                            <td>{{ $faktur->kode_transaksi }}</td>
                                            <td>{{ $faktur->npwp_nik_pembeli }}</td>
                                            <td>{{ $faktur->nama_pembeli }}</td>
                                            <td>{{ $faktur->nomor_faktur }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('laporan_faktur.show', $faktur->id) }}" class="btn btn-info btn-sm" title="Lihat Detail">
                                                        <i class="fas fa-eye"></i> Detail
                                                    </a>
                                                    <a href="{{ route('efaktur.export.single', $faktur->id) }}" class="btn btn-success btn-sm" title="Export XML">
                                                        <i class="fas fa-file-export"></i> XML
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $fakturs->links() }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            Belum ada data laporan e-faktur.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">
                    <i class="fas fa-file-export me-2"></i>
                    Export E-Faktur ke XML
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="exportForm" action="{{ route('efaktur.export.date-range') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Pilih Metode Export:</label>
                        <div class="d-grid gap-2">
                            <a href="{{ route('efaktur.export.all') }}" class="btn btn-outline-primary">
                                <i class="fas fa-download me-1"></i>
                                Export Semua Data
                            </a>
                            <button type="button" class="btn btn-outline-primary" onclick="showDateRange()">
                                <i class="fas fa-calendar-alt me-1"></i>
                                Export Berdasarkan Tanggal
                            </button>
                            <button type="button" class="btn btn-outline-primary" onclick="previewXML()">
                                <i class="fas fa-eye me-1"></i>
                                Preview XML
                            </button>
                        </div>
                    </div>

                    <div id="dateRangeForm" class="mt-3" style="display: none;">
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Tanggal Mulai:</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="end_date" class="form-label">Tanggal Selesai:</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" required>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-download me-1"></i>
                            Export
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">
                    <i class="fas fa-code me-2"></i>
                    XML Preview
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <pre id="xmlPreviewContent" class="bg-light p-3 rounded" style="max-height: 500px; overflow-y: auto;"></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="copyXMLToClipboard()">
                    <i class="fas fa-copy me-1"></i>
                    Copy to Clipboard
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table th {
        vertical-align: middle;
        white-space: nowrap;
    }
    .btn-group .btn {
        margin-left: 0.25rem;
    }
    .btn-group .btn:first-child {
        margin-left: 0;
    }
    #xmlPreviewContent {
        font-family: 'Courier New', monospace;
        font-size: 12px;
        line-height: 1.4;
        white-space: pre-wrap;
        word-wrap: break-word;
    }
</style>
@endpush

@push('scripts')
<script>
function showDateRange() {
    document.getElementById('dateRangeForm').style.display = 'block';
}

function previewXML() {
    fetch('{{ route("efaktur.export.preview") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('xmlPreviewContent').textContent = data.xml_content;
            new bootstrap.Modal(document.getElementById('previewModal')).show();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error generating XML preview');
    });
}

function copyXMLToClipboard() {
    const xmlContent = document.getElementById('xmlPreviewContent').textContent;
    navigator.clipboard.writeText(xmlContent).then(() => {
        alert('XML content copied to clipboard!');
    }).catch(err => {
        console.error('Error copying to clipboard:', err);
        alert('Failed to copy to clipboard');
    });
}
</script>
@endpush 