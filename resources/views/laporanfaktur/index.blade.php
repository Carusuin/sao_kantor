@extends('dashboard_layout')

@section('title', 'Laporan E-Faktur')

@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

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
                        <button type="button" class="btn btn-success" id="addRowBtn">
                            <i class="fas fa-plus me-1"></i> Tambah Baris
                        </button>
                        <a href="{{ route('laporan_faktur.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>
                            Tambah Laporan E-Faktur
                        </a>
                        <form id="exportSelectedForm" action="{{ route('efaktur.export.multiple') }}" method="POST" style="display:inline;">
                            @csrf
                            <input type="hidden" name="selected_ids" id="selectedIdsInput">
                            <button type="button" class="btn btn-success" id="exportSelectedBtn">
                                <i class="fas fa-file-export me-1"></i>
                                Export XML
                            </button>
                        </form>
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
                                        <th style="width: 40px; text-align: center;">
                                            <input type="checkbox" id="selectAllCheckbox" onclick="toggleAllCheckboxes(this)">
                                        </th>
                                        <th>No</th>
                                        <th>Tanggal Faktur</th>
                                        <th>Identitas Pembeli</th>
                                        <th>Nama Pembeli</th>
                                        <th>Alamat</th>
                                        <th>Email</th>
                                        <th>No Invoice/No Dokumen/No Referensi/No Nota</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="fakturTableBody">
                                    @foreach($fakturs as $faktur)
                                        <tr>
                                            <td style="text-align: center;">
                                                <input type="checkbox" class="rowCheckbox" name="selected_fakturs[]" value="{{ $faktur->id }}">
                                            </td>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $faktur->tanggal_faktur->format('d-m-Y') }}</td>
                                            <td>{{ $faktur->jenis_id_pembeli }} {{ $faktur->npwp_nik_pembeli }}</td>
                                            <td>{{ $faktur->nama_pembeli }}</td>
                                            <td>{{ $faktur->alamat_pembeli }}</td>
                                            <td>{{ $faktur->email_pembeli }}</td>
                                            <td>{{ $faktur->referensi }}</td>
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

function toggleAllCheckboxes(source) {
    const checkboxes = document.querySelectorAll('.rowCheckbox');
    checkboxes.forEach(cb => cb.checked = source.checked);
}

let fakturFormRowCount = 0;

function getFormRowHtml(rowNum) {
    return `
    <tr class="faktur-form-row">
        <td></td>
        <td class="auto-number"></td>
        <td><input type="date" class="form-control" name="tanggal_faktur[]"></td>
        <td>
            <select class="form-select identitas-type" name="jenis_id_pembeli[]">
                <option value="NPWP">NPWP</option>
                <option value="NIK">NIK</option>
                <option value="Passport">Passport</option>
                <option value="Lainnya">Lainnya</option>
            </select>
            <input type="text" class="form-control mt-1 identitas-value" name="npwp_nik_pembeli[]" placeholder="Nomor Identitas">
            <div class="invalid-feedback"></div>
        </td>
        <td><input type="text" class="form-control" name="nama_pembeli[]"></td>
        <td><input type="text" class="form-control" name="alamat_pembeli[]"></td>
        <td><input type="email" class="form-control" name="email_pembeli[]"></td>
        <td><input type="text" class="form-control" name="referensi[]"></td>
        <td>
            <button type="button" class="btn btn-success btn-sm simpanBarisBtn">Simpan</button>
            <button type="button" class="btn btn-danger btn-sm hapusBarisBtn">Hapus</button>
        </td>
    </tr>
    `;
}

function updateAutoNumbers() {
    let rows = document.querySelectorAll('#fakturTableBody tr');
    let num = 1;
    rows.forEach(row => {
        let autoNum = row.querySelector('.auto-number');
        if (autoNum) {
            autoNum.textContent = num++;
        } else if (row.querySelector('td:nth-child(2)')) {
            row.querySelector('td:nth-child(2)').textContent = num++;
        }
    });
}

document.getElementById('addRowBtn').addEventListener('click', function() {
    fakturFormRowCount++;
    let tbody = document.getElementById('fakturTableBody');
    tbody.insertAdjacentHTML('afterbegin', getFormRowHtml(fakturFormRowCount));
    updateAutoNumbers();
});

function getDrafts() {
    const drafts = [];
    for (let i = 0; i < sessionStorage.length; i++) {
        const key = sessionStorage.key(i);
        if (key.startsWith('faktur_temp_')) {
            try {
                const data = JSON.parse(sessionStorage.getItem(key));
                if (data) drafts.push(data);
            } catch(e) {}
        }
    }
    // Urutkan berdasarkan waktu pembuatan (id mengandung timestamp)
    drafts.sort((a, b) => (a.id > b.id ? -1 : 1));
    return drafts;
}

function renderDraftRow(draft) {
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td></td>
        <td class="auto-number"></td>
        <td>${draft.tanggal_faktur ? draft.tanggal_faktur.split('-').reverse().join('-') : ''}</td>
        <td>${draft.jenis_id_pembeli || ''} ${draft.npwp_nik_pembeli || ''}</td>
        <td>${draft.nama_pembeli || ''}</td>
        <td>${draft.alamat_pembeli || ''}</td>
        <td>${draft.email_pembeli || ''}</td>
        <td>${draft.referensi || ''}</td>
        <td>
            <div class="btn-group">
                <a href="{{ route('laporan_faktur.create') }}?temp_id=${draft.id}" class="btn btn-warning btn-sm" title="Isi / Lengkapi Data">
                    <i class="fas fa-edit"></i> Isi
                </a>
                <button type="button" class="btn btn-danger btn-sm hapusDraftBtn" data-draft-id="${draft.id}"><i class="fas fa-trash"></i></button>
            </div>
        </td>
    `;
    return newRow;
}

function renderAllDrafts() {
    const tbody = document.getElementById('fakturTableBody');
    const drafts = getDrafts();
    drafts.forEach(draft => {
        tbody.insertBefore(renderDraftRow(draft), tbody.firstChild);
    });
    updateAutoNumbers();
}

document.addEventListener('DOMContentLoaded', function() {
    renderAllDrafts();
});

document.getElementById('fakturTableBody').addEventListener('click', function(e) {
    if (e.target.classList.contains('hapusBarisBtn')) {
        e.target.closest('tr').remove();
        updateAutoNumbers();
    }
    const btn = e.target.closest('.hapusDraftBtn');
    if (btn) {
        const draftId = btn.getAttribute('data-draft-id');
        sessionStorage.removeItem(draftId);
        btn.closest('tr').remove();
        updateAutoNumbers();
    }
    if (e.target.classList.contains('simpanBarisBtn')) {
        const row = e.target.closest('tr');
        // Ambil data dari input
        const tanggal_faktur = row.querySelector('input[name="tanggal_faktur[]"]').value;
        const jenis_id_pembeli = row.querySelector('select[name="jenis_id_pembeli[]"]').value;
        const npwp_nik_pembeli = row.querySelector('input[name="npwp_nik_pembeli[]"]').value;
        const nama_pembeli = row.querySelector('input[name="nama_pembeli[]"]').value;
        const alamat_pembeli = row.querySelector('input[name="alamat_pembeli[]"]').value;
        const email_pembeli = row.querySelector('input[name="email_pembeli[]"]').value;
        const referensi = row.querySelector('input[name="referensi[]"]').value;

        // Validasi sederhana (bisa dikembangkan sesuai kebutuhan)
        let valid = true;
        let errorMsg = '';
        if (!tanggal_faktur) { valid = false; errorMsg = 'Tanggal wajib diisi'; }
        if (!jenis_id_pembeli) { valid = false; errorMsg = 'Jenis identitas wajib dipilih'; }
        if (!npwp_nik_pembeli) { valid = false; errorMsg = 'Nomor identitas wajib diisi'; }
        if (!nama_pembeli) { valid = false; errorMsg = 'Nama pembeli wajib diisi'; }
        if (!alamat_pembeli) { valid = false; errorMsg = 'Alamat wajib diisi'; }
        // Email opsional, validasi jika diisi
        if (email_pembeli && !/^\S+@\S+\.\S+$/.test(email_pembeli)) { valid = false; errorMsg = 'Format email tidak valid'; }

        // Validasi khusus identitas
        if (jenis_id_pembeli === 'NPWP' && !/^\d{16}$/.test(npwp_nik_pembeli)) { valid = false; errorMsg = 'NPWP harus 16 digit angka'; }
        if (jenis_id_pembeli === 'NIK' && !/^\d{16}$/.test(npwp_nik_pembeli)) { valid = false; errorMsg = 'NIK harus 16 digit angka'; }
        if ((jenis_id_pembeli === 'Passport' || jenis_id_pembeli === 'Lainnya') && npwp_nik_pembeli.length > 20) { valid = false; errorMsg = 'Maksimal 20 karakter'; }

        // Reset error
        row.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        row.querySelectorAll('input, select').forEach(el => el.classList.remove('is-invalid'));

        if (!valid) {
            // Tampilkan error di bawah field identitas
            row.querySelector('.identitas-value').classList.add('is-invalid');
            row.querySelector('.invalid-feedback').textContent = errorMsg;
            return;
        }

        // Simpan data ke sessionStorage (atau localStorage)
        // Buat id unik (timestamp + random)
        const tempId = 'faktur_temp_' + Date.now() + '_' + Math.floor(Math.random()*10000);
        const fakturData = {
            id: tempId,
            tanggal_faktur,
            jenis_id_pembeli,
            npwp_nik_pembeli,
            nama_pembeli,
            alamat_pembeli,
            email_pembeli,
            referensi
        };
        sessionStorage.setItem(tempId, JSON.stringify(fakturData));

        // Render baris data biasa (dengan tombol Isi, passing tempId)
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td></td>
            <td class="auto-number"></td>
            <td>${tanggal_faktur.split('-').reverse().join('-')}</td>
            <td>${jenis_id_pembeli} ${npwp_nik_pembeli}</td>
            <td>${nama_pembeli}</td>
            <td>${alamat_pembeli}</td>
            <td>${email_pembeli}</td>
            <td>${referensi}</td>
            <td>
                <div class="btn-group">
                    <a href="{{ route('laporan_faktur.create') }}?temp_id=${tempId}" class="btn btn-warning btn-sm" title="Isi / Lengkapi Data">
                        <i class="fas fa-edit"></i> Isi
                    </a>
                    <button type="button" class="btn btn-danger btn-sm hapusDraftBtn" data-draft-id="${tempId}"><i class="fas fa-trash"></i></button>
                </div>
            </td>
        `;
        row.parentNode.replaceChild(newRow, row);
        updateAutoNumbers();
    }
});

document.getElementById('exportSelectedBtn').addEventListener('click', function() {
    const checked = document.querySelectorAll('.rowCheckbox:checked');
    if (checked.length === 0) {
        alert('Pilih minimal satu transaksi yang ingin diexport!');
        return;
    }
    const ids = Array.from(checked).map(cb => cb.value);
    document.getElementById('selectedIdsInput').value = JSON.stringify(ids);
    document.getElementById('exportSelectedForm').submit();
});
</script>
@endpush 