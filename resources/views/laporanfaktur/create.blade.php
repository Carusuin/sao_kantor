@extends('dashboard_layout')

@section('title', 'Buat Faktur Pajak Keluaran')
@section('page-title', 'Buat Faktur Pajak Keluaran')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-file-invoice me-2"></i>Buat Faktur Pajak Keluaran Baru
                    </h4>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('laporan_faktur.store') }}" method="POST" id="createEFakturForm">
                        @csrf
                        
                        <!-- Dokumen Transaksi Section -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Dokumen Transaksi</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="uangMuka" name="uang_muka" value="1">
                                            <label class="form-check-label" for="uangMuka">Uang Muka</label>
                                </div>
                            </div>
                                    <div class="col-md-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="pelunasan" name="pelunasan" value="1">
                                            <label class="form-check-label" for="pelunasan">Pelunasan</label>
                                </div>
                            </div>
                                    <div class="col-md-4">
                                        <label for="nomorFaktur" class="form-label">Nomor Faktur <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nomorFaktur" name="nomor_faktur" 
                                            placeholder="Nomor Faktur" required
                                            pattern="[A-Z0-9.-]+" 
                                            title="Nomor faktur hanya boleh berisi huruf kapital, angka, titik, dan tanda hubung"
                                            maxlength="255">
                                        <div class="invalid-feedback">
                                            Nomor faktur harus diisi dan hanya boleh berisi huruf kapital, angka, titik, dan tanda hubung
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="kodeTransaksi" class="form-label">Kode Transaksi <span class="text-danger">*</span></label>
                                        <select class="form-select" id="kodeTransaksi" name="kode_transaksi" required>
                                            <option value="">Pilih Kode Transaksi</option>
                                            <option value="01">01 - Penyerahan BKP/JKP</option>
                                            <option value="02">02 - Penyerahan BKP/JKP kepada Pemungut PPN</option>
                                            <option value="03">03 - Penyerahan BKP/JKP kepada Instansi Pemerintah</option>
                                            <option value="04">04 - DPP Tidak Dipungut</option>
                                            <option value="06">06 - DPP Dibebaskan</option>
                                            <option value="07">07 - Penyerahan yang PPN-nya Ditanggung Pemerintah</option>
                                            <option value="08">08 - Penyerahan yang PPN-nya Tidak Dipungut</option>
                                        </select>
                        </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="tanggalFaktur" class="form-label">Tanggal Faktur <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="tanggalFaktur" name="tanggal_faktur" value="{{ now()->format('Y-m-d') }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="jenisFaktur" class="form-label">Jenis Faktur <span class="text-danger">*</span></label>
                                        <select class="form-select" id="jenisFaktur" name="jenis_faktur" required>
                                            <option value="Normal">Normal</option>
                                            <option value="Pengganti">Pengganti</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="masaPajak" class="form-label">Masa Pajak <span class="text-danger">*</span></label>
                                        <select class="form-select" id="masaPajak" name="masa_pajak" required>
                                            @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $key => $month)
                                                <option value="{{ $key + 1 }}" {{ (date('n') == ($key + 1)) ? 'selected' : '' }}>{{ $month }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="tahunPajak" class="form-label">Tahun <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="tahunPajak" name="tahun_pajak" value="{{ date('Y') }}" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="npwpPenjual" class="form-label">NPWP Penjual <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="npwpPenjual" name="npwp_penjual" placeholder="0013575832046000" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="referensi" class="form-label">Referensi</label>
                                        <input type="text" class="form-control" id="referensi" name="referensi" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="alamatDokumen" class="form-label">Alamat <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="alamatDokumen" name="alamat_dokumen" placeholder="JL. HOS COKROAMINOTO N..." required>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="idTKUdokumen" class="form-label">IDTKU <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="idTKUdokumen" name="id_tku_dokumen" placeholder="000000" readonly required>
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
                                        <label for="npwpPembeli" class="form-label">NPWP <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="npwpPembeli" name="npwp_pembeli" placeholder="0013575832046000" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">ID</label>
                                        <div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="buyer_id_type" id="idNpwp" value="NPWP" checked>
                                                <label class="form-check-label" for="idNpwp">NPWP</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="buyer_id_type" id="idPaspor" value="Paspor">
                                                <label class="form-check-label" for="idPaspor">Paspor</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="buyer_id_type" id="idNik" value="NIK">
                                                <label class="form-check-label" for="idNik">NIK</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="buyer_id_type" id="idLain" value="Lainnya">
                                                <label class="form-check-label" for="idLain">Lainnya</label>
                                </div>
                            </div>
                        </div>
                                    <div class="col-md-4">
                                        <label for="negaraPembeli" class="form-label">Negara</label>
                                        <select class="form-select" id="negaraPembeli" name="negara_pembeli">
                                            <option value="IDN" selected>Indonesia</option>
                                            <option value="SGP">Singapore</option>
                                            <option value="MYS">Malaysia</option>
                                            <option value="THA">Thailand</option>
                                            <option value="VNM">Vietnam</option>
                                            <option value="PHL">Philippines</option>
                                            <option value="BRN">Brunei</option>
                                            <option value="KHM">Cambodia</option>
                                            <option value="LAO">Laos</option>
                                            <option value="MMR">Myanmar</option>
                                            <option value="TLS">East Timor</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="nomorDokumenPembeli" class="form-label">Nomor Dokumen</label>
                                        <input type="text" class="form-control" id="nomorDokumenPembeli" name="nomor_dokumen_pembeli">
                            </div>
                                    <div class="col-md-4">
                                        <label for="namaPembeli" class="form-label">Nama <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="namaPembeli" name="nama_pembeli" placeholder="SERAS*********" required>
                        </div>
                                    <div class="col-md-4">
                                        <label for="alamatPembeli" class="form-label">Alamat <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="alamatPembeli" name="alamat_pembeli" placeholder="JL. MITRA SUNTER BOULEVARD B..." required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="idTKUPembeli" class="form-label">IDTKU <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="idTKUPembeli" name="id_tku_pembeli" placeholder="000000" readonly required>
                            </div>
                                    <div class="col-md-4">
                                        <label for="emailPembeli" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="emailPembeli" name="email_pembeli" placeholder="taxsera.ho@sera.astra.co.id">
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
                                <div class="mb-3">
                                    <button type="button" class="btn btn-primary btn-sm me-2" id="addTransactionRow">
                                        <i class="fas fa-plus me-1"></i>Tambah Transaksi
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" id="removeSelectedRows">
                                        <i class="fas fa-trash-alt me-1"></i>Hapus Transaksi
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="transactionTable">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th class="text-center" style="width: 30px;">
                                                    <input type="checkbox" id="checkAll">
                                                </th>
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
                                            <!-- Transaction rows will be added here by JavaScript -->
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="7" class="text-end"><strong>JUMLAH</strong></td>
                                                <td class="text-end total-harga-sum">0,00</td>
                                                <td class="text-end potongan-harga-sum">0,00</td>
                                                <td></td>
                                                <td class="text-end dpp-sum">0,00</td>
                                                <td class="text-end ppn-sum">0,00</td>
                                                <td class="text-end dpp-nilai-lain-sum">0,00</td>
                                                <td class="text-end ppnbm-sum">0,00</td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
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
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Simpan Faktur
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi (Keep existing if it's used elsewhere, otherwise this can be adapted) -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Simpan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menyimpan faktur pajak ini?</p>
                <div class="alert alert-info">
                    <small><i class="fas fa-info-circle me-2"></i>
                    Pastikan semua data sudah benar sebelum menyimpan.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmSave">Ya, Simpan</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let rowCounter = 0;

    // Function to generate IDTKU from NPWP/TIN
    function generateIDTKU(npwp) {
        if (!npwp) return '';
        // Remove any non-numeric characters
        npwp = npwp.replace(/\D/g, '');
        // Add 6 zeros at the end
        return npwp + '000000';
    }

    // Update seller IDTKU when NPWP changes
    $('#npwpPenjual').on('input', function() {
        const npwp = $(this).val();
        $('#idTKUdokumen').val(generateIDTKU(npwp));
    });

    // Update buyer IDTKU when NPWP changes
    $('#npwpPembeli').on('input', function() {
        const npwp = $(this).val();
        $('#idTKUPembeli').val(generateIDTKU(npwp));
    });

    // Function to update referensi based on nomor dokumen
    function updateReferensi() {
        const nomorDokumen = $('#nomorDokumenPembeli').val();
        $('#referensi').val(nomorDokumen);
    }

    // Listen for changes in nomor dokumen
    $('#nomorDokumenPembeli').on('input', function() {
        updateReferensi();
    });

    // Initial update of referensi and IDTKU fields
    updateReferensi();
    $('#npwpPenjual').trigger('input');
    $('#npwpPembeli').trigger('input');

    function addTransactionRow(data = {}) {
        rowCounter++;
        let unitOptions = '';
        @foreach($units as $unit)
            unitOptions += `<option value="{{ $unit['code'] }}" ${(data.nama_satuan_ukur === '{{ $unit['code'] }}') ? 'selected' : ''}>{{ $unit['name'] }}</option>`;
        @endforeach
        const newRow = `
            <tr data-row-id="${rowCounter}">
                <td class="text-center">
                    <input type="checkbox" class="row-check">
                </td>
                <td>
                    <select class="form-select form-select-sm item-type" name="items[${rowCounter}][jenis_barang_jasa]">
                        <option value="B" ${data.jenis_barang_jasa === 'B' ? 'selected' : ''}>Barang</option>
                        <option value="J" ${data.jenis_barang_jasa === 'J' ? 'selected' : ''}>Jasa</option>
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm item-name" name="items[${rowCounter}][nama_barang_jasa]" value="${data.nama_barang_jasa || ''}" required>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm item-code" name="items[${rowCounter}][kode_barang_jasa]" value="${data.kode_barang_jasa || ''}" required>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm item-qty" name="items[${rowCounter}][jumlah_barang_jasa]" value="${data.jumlah_barang_jasa || ''}" step="any" min="0" required>
                </td>
                <td>
                    <select class="form-select form-select-sm item-unit" name="items[${rowCounter}][nama_satuan_ukur]" required>
                        ${unitOptions}
                    </select>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm item-price" name="items[${rowCounter}][harga_satuan]" value="${data.harga_satuan || ''}" step="any" min="0" required>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm item-total" name="items[${rowCounter}][total_harga]" value="" step="any" min="0" readonly>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm item-discount" name="items[${rowCounter}][total_diskon]" value="0" step="any" min="0" readonly>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm item-vat-rate" name="items[${rowCounter}][tarif_ppn]" value="12" step="any" min="0" max="100" readonly>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm item-dpp" name="items[${rowCounter}][dpp]" value="" step="any" min="0" readonly>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm item-ppn" name="items[${rowCounter}][ppn]" value="" step="any" min="0" readonly>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm item-dpp-lain" name="items[${rowCounter}][dpp_nilai_lain]" value="" step="any" min="0" readonly>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm item-ppnbm" name="items[${rowCounter}][ppnbm]" value="0" step="any" min="0" readonly>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm item-ppnbm-rate" name="items[${rowCounter}][tarif_ppnbm]" value="0" step="any" min="0" max="100" readonly>
                </td>
            </tr>
        `;
        $('#transactionTable tbody').append(newRow);
        updateTotals();
    }

    function calculateRow(row) {
        const qty = parseFloat(row.find('.item-qty').val()) || 0;
        const price = parseFloat(row.find('.item-price').val()) || 0;
        // Rumus
        const totalHarga = price * qty;
        const potonganHarga = 0;
        const tarifPPN = 12;
        const dpp = totalHarga;
        // Pembulatan sesuai rumus Excel untuk DPP Nilai Lain
        let dppNilaiLain = Math.round(Math.round(dpp * 11 / 12 * 100) / 100);
        // Pembulatan sesuai rumus Excel untuk PPN
        let ppn = Math.round(Math.round(dppNilaiLain * 12 * 100) / 10000);
        const ppnbm = 0;
        const tarifPPNBM = 0;

        row.find('.item-total').val(totalHarga ? totalHarga.toFixed(2) : '');
        row.find('.item-discount').val('0');
        row.find('.item-vat-rate').val('12');
        row.find('.item-dpp').val(dpp ? dpp.toFixed(2) : '');
        row.find('.item-dpp-lain').val(dppNilaiLain ? dppNilaiLain : '');
        row.find('.item-ppn').val(ppn ? ppn : '');
        row.find('.item-ppnbm').val('0');
        row.find('.item-ppnbm-rate').val('0');
    }

    function updateTotals() {
        let totalHargaSum = 0;
        let potonganHargaSum = 0;
        let dppSum = 0;
        let ppnSum = 0;
        let dppNilaiLainSum = 0;
        let ppnbmSum = 0;

        $('#transactionTable tbody tr').each(function() {
            const row = $(this);
            calculateRow(row); // Recalculate each row before summing

            totalHargaSum += parseFloat(row.find('.item-total').val()) || 0;
            potonganHargaSum += parseFloat(row.find('.item-discount').val()) || 0;
            dppSum += parseFloat(row.find('.item-dpp').val()) || 0;
            ppnSum += parseFloat(row.find('.item-ppn').val()) || 0;
            dppNilaiLainSum += parseFloat(row.find('.item-dpp-lain').val()) || 0;
            ppnbmSum += parseFloat(row.find('.item-ppnbm').val()) || 0;
        });

        $('.total-harga-sum').text(totalHargaSum.toFixed(2));
        $('.potongan-harga-sum').text(potonganHargaSum.toFixed(2));
        $('.dpp-sum').text(dppSum.toFixed(2));
        $('.ppn-sum').text(ppnSum.toFixed(2));
        $('.dpp-nilai-lain-sum').text(dppNilaiLainSum.toFixed(2));
        $('.ppnbm-sum').text(ppnbmSum.toFixed(2));
    }

    // Add first row on page load
    addTransactionRow();

    // Event listeners
    $('#addTransactionRow').click(addTransactionRow);
        
    $('#checkAll').change(function() {
        $('.row-check').prop('checked', $(this).prop('checked'));
    });

    $('#removeSelectedRows').click(function() {
        $('#transactionTable tbody .row-check:checked').closest('tr').remove();
        updateTotals();
    });

    $('#transactionTable').on('input', '.item-qty, .item-price', function() {
        calculateRow($(this).closest('tr'));
        updateTotals();
    });
    
    // Form submission with confirmation modal
    $('#createEFakturForm').on('submit', function(e) {
        e.preventDefault();
        
        // Show confirmation modal
        const confirmModal = new bootstrap.Modal($('#confirmModal')[0]);
        confirmModal.show();
    });
    
    // Confirm save
    $('#confirmSave').click(function() {
        // Hide modal
        const confirmModal = bootstrap.Modal.getInstance($('#confirmModal')[0]);
        confirmModal.hide();
        
        // Submit form
        $('#createEFakturForm')[0].submit();
    });
    
    // Initial calculation when page loads (useful if initial data is present)
    updateTotals();
});
</script>
@endpush

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

.form-control:focus,
.form-select:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn {
    border-radius: 8px;
    font-weight: 500;
    padding: 8px 16px;
}

.input-group-text {
    background-color: #f8f9fa;
    border-color: #ced4da;
    font-weight: 500;
}

.toast {
    z-index: 1055;
}

.invalid-feedback {
    font-size: 0.875rem;
}

.alert-info {
    background-color: #d1ecf1;
    border-color: #bee5eb;
    color: #0c5460;
}

.modal-content {
    border-radius: 10px;
    border: none;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
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