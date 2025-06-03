
@extends('layouts.app')

@section('title', 'Edit Laporan Pajak')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Edit Laporan Pajak
                    </h4>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('laporan.update', $laporan->id) }}" method="POST" id="editLaporanForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Informasi Dasar Laporan -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tax_period_month" class="form-label">Periode Bulan Pajak</label>
                                    <select class="form-select @error('tax_period_month') is-invalid @enderror" 
                                            id="tax_period_month" name="tax_period_month" required>
                                        <option value="">Pilih Bulan</option>
                                        @for($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}" 
                                                {{ old('tax_period_month', $laporan->tax_period_month) == $i ? 'selected' : '' }}>
                                                {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('tax_period_month')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tax_period_year" class="form-label">Periode Tahun Pajak</label>
                                    <select class="form-select @error('tax_period_year') is-invalid @enderror" 
                                            id="tax_period_year" name="tax_period_year" required>
                                        <option value="">Pilih Tahun</option>
                                        @for($year = date('Y'); $year >= 2020; $year--)
                                            <option value="{{ $year }}" 
                                                {{ old('tax_period_year', $laporan->tax_period_year) == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('tax_period_year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="trx_code" class="form-label">Kode Transaksi</label>
                                    <select class="form-select @error('trx_code') is-invalid @enderror" 
                                            id="trx_code" name="trx_code" required>
                                        <option value="">Pilih Kode</option>
                                        <option value="Normal" {{ old('trx_code', $laporan->trx_code) == 'Normal' ? 'selected' : '' }}>Normal</option>
                                        <option value="Pembetulan" {{ old('trx_code', $laporan->trx_code) == 'Pembetulan' ? 'selected' : '' }}>Pembetulan</option>
                                    </select>
                                    @error('trx_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="buyer_name" class="form-label">Nama Pembeli</label>
                                    <input type="text" class="form-control @error('buyer_name') is-invalid @enderror" 
                                           id="buyer_name" name="buyer_name" 
                                           value="{{ old('buyer_name', $laporan->buyer_name) }}" 
                                           placeholder="Masukkan nama pembeli" required>
                                    @error('buyer_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="buyer_id_opt" class="form-label">Opsi ID Pembeli</label>
                                    <select class="form-select @error('buyer_id_opt') is-invalid @enderror" 
                                            id="buyer_id_opt" name="buyer_id_opt" required>
                                        <option value="">Pilih Opsi</option>
                                        <option value="NIK" {{ old('buyer_id_opt', $laporan->buyer_id_opt) == 'NIK' ? 'selected' : '' }}>NIK</option>
                                        <option value="NPWP" {{ old('buyer_id_opt', $laporan->buyer_id_opt) == 'NPWP' ? 'selected' : '' }}>NPWP</option>
                                        <option value="Passport" {{ old('buyer_id_opt', $laporan->buyer_id_opt) == 'Passport' ? 'selected' : '' }}>Passport</option>
                                    </select>
                                    @error('buyer_id_opt')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="buyer_id_number" class="form-label">Nomor ID Pembeli</label>
                                    <input type="text" class="form-control @error('buyer_id_number') is-invalid @enderror" 
                                           id="buyer_id_number" name="buyer_id_number" 
                                           value="{{ old('buyer_id_number', $laporan->buyer_id_number) }}" 
                                           placeholder="16 digit angka (0 jika kosong)" 
                                           maxlength="16" required>
                                    @error('buyer_id_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="good_service_opt" class="form-label">Opsi Barang/Jasa</label>
                                    <select class="form-select @error('good_service_opt') is-invalid @enderror" 
                                            id="good_service_opt" name="good_service_opt" required>
                                        <option value="">Pilih Opsi</option>
                                        <option value="A" {{ old('good_service_opt', $laporan->good_service_opt) == 'A' ? 'selected' : '' }}>A - Barang</option>
                                        <option value="B" {{ old('good_service_opt', $laporan->good_service_opt) == 'B' ? 'selected' : '' }}>B - Jasa</option>
                                    </select>
                                    @error('good_service_opt')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="serial_no" class="form-label">Nomor Seri</label>
                                    <input type="text" class="form-control @error('serial_no') is-invalid @enderror" 
                                           id="serial_no" name="serial_no" 
                                           value="{{ old('serial_no', $laporan->serial_no) }}" 
                                           placeholder="Masukkan nomor seri" required>
                                    @error('serial_no')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="transaction_date" class="form-label">Tanggal Transaksi</label>
                                    <input type="date" class="form-control @error('transaction_date') is-invalid @enderror" 
                                           id="transaction_date" name="transaction_date" 
                                           value="{{ old('transaction_date', $laporan->transaction_date ? $laporan->transaction_date->format('Y-m-d') : '') }}" 
                                           required>
                                    @error('transaction_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tax_base_selling_price" class="form-label">Harga Jual Dasar Pajak</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control @error('tax_base_selling_price') is-invalid @enderror" 
                                               id="tax_base_selling_price" name="tax_base_selling_price" 
                                               value="{{ old('tax_base_selling_price', $laporan->tax_base_selling_price) }}" 
                                               placeholder="0" step="0.01" required>
                                    </div>
                                    @error('tax_base_selling_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="other_tax_selling_price" class="form-label">Harga Jual Pajak Lainnya</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control @error('other_tax_selling_price') is-invalid @enderror" 
                                               id="other_tax_selling_price" name="other_tax_selling_price" 
                                               value="{{ old('other_tax_selling_price', $laporan->other_tax_selling_price) }}" 
                                               placeholder="0" step="0.01">
                                    </div>
                                    @error('other_tax_selling_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="vat" class="form-label">PPN (Pajak Pertambahan Nilai)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control @error('vat') is-invalid @enderror" 
                                               id="vat" name="vat" 
                                               value="{{ old('vat', $laporan->vat) }}" 
                                               placeholder="0" step="0.01" readonly>
                                        <span class="input-group-text">
                                            <i class="fas fa-calculator" title="Otomatis dihitung"></i>
                                        </span>
                                    </div>
                                    @error('vat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="stlg" class="form-label">STLG (Status)</label>
                                    <select class="form-select @error('stlg') is-invalid @enderror" 
                                            id="stlg" name="stlg" required>
                                        <option value="">Pilih Status</option>
                                        <option value="0" {{ old('stlg', $laporan->stlg) == '0' ? 'selected' : '' }}>0 - Normal</option>
                                        <option value="1" {{ old('stlg', $laporan->stlg) == '1' ? 'selected' : '' }}>1 - Pembatalan</option>
                                    </select>
                                    @error('stlg')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="info" class="form-label">Informasi Tambahan</label>
                                    <input type="text" class="form-control @error('info') is-invalid @enderror" 
                                           id="info" name="info" 
                                           value="{{ old('info', $laporan->info) }}" 
                                           placeholder="ok atau informasi lainnya">
                                    @error('info')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <a href="{{ route('laporan.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left me-2"></i>Kembali
                                        </a>
                                        <a href="{{ route('laporan.show', $laporan->id) }}" class="btn btn-info">
                                            <i class="fas fa-eye me-2"></i>Lihat Detail
                                        </a>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-warning" id="calculateVAT">
                                            <i class="fas fa-calculator me-2"></i>Hitung PPN
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Update Laporan
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

<!-- Modal Konfirmasi -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Update</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin mengupdate laporan pajak ini?</p>
                <div class="alert alert-info">
                    <small><i class="fas fa-info-circle me-2"></i>
                    Pastikan semua data sudah benar sebelum menyimpan perubahan.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmUpdate">Ya, Update</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto calculate VAT when tax base selling price changes
    function calculateVAT() {
        const taxBase = parseFloat($('#tax_base_selling_price').val()) || 0;
        const vatRate = 0.11; // 11% VAT rate
        const vat = Math.round(taxBase * vatRate);
        $('#vat').val(vat);
    }
    
    // Calculate VAT on price input change
    $('#tax_base_selling_price').on('input', calculateVAT);
    
    // Manual VAT calculation button
    $('#calculateVAT').click(function() {
        calculateVAT();
        
        // Show toast notification
        const toast = $('<div class="toast position-fixed top-0 end-0 m-3" role="alert">' +
            '<div class="toast-header">' +
                '<i class="fas fa-calculator text-success me-2"></i>' +
                '<strong class="me-auto">Perhitungan PPN</strong>' +
                '<button type="button" class="btn-close" data-bs-dismiss="toast"></button>' +
            '</div>' +
            '<div class="toast-body">PPN berhasil dihitung otomatis!</div>' +
        '</div>');
        
        $('body').append(toast);
        const bsToast = new bootstrap.Toast(toast[0]);
        bsToast.show();
        
        toast.on('hidden.bs.toast', function() {
            $(this).remove();
        });
    });
    
    // Form validation
    $('#editLaporanForm').on('submit', function(e) {
        e.preventDefault();
        
        // Show confirmation modal
        const confirmModal = new bootstrap.Modal($('#confirmModal')[0]);
        confirmModal.show();
    });
    
    // Confirm update
    $('#confirmUpdate').click(function() {
        // Hide modal
        const confirmModal = bootstrap.Modal.getInstance($('#confirmModal')[0]);
        confirmModal.hide();
        
        // Submit form
        $('#editLaporanForm')[0].submit();
    });
    
    // Auto-format number inputs
    $('input[type="number"]').on('blur', function() {
        const value = parseFloat($(this).val());
        if (!isNaN(value)) {
            $(this).val(value.toFixed(2));
        }
    });
    
    // Validate buyer ID number format
    $('#buyer_id_number').on('input', function() {
        let value = $(this).val().replace(/\D/g, ''); // Remove non-digits
        if (value.length > 16) {
            value = value.substring(0, 16);
        }
        $(this).val(value);
    });
    
    // Auto-calculate VAT on page load
    calculateVAT();
    
    // Real-time form validation feedback
    $('input, select').on('blur', function() {
        const field = $(this);
        const value = field.val().trim();
        
        if (field.prop('required') && !value) {
            field.addClass('is-invalid');
            if (!field.next('.invalid-feedback').length) {
                field.after('<div class="invalid-feedback">Field ini wajib diisi.</div>');
            }
        } else {
            field.removeClass('is-invalid');
            field.next('.invalid-feedback').remove();
        }
    });
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
</style>
@endpush