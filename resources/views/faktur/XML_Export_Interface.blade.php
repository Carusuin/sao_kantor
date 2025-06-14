{{-- resources/views/laporan/xml-export.blade.php --}}
@extends('layouts.app')

@section('title', 'XML Export Interface')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-export me-2"></i>
                        XML Tax Report Export Interface
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Export Form -->
                    <form id="xmlExportForm" action="{{ route('laporan.export.xml') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-4">
                            <!-- TIN Input -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tin" class="form-label required">
                                        <i class="fas fa-id-card me-1"></i>
                                        TIN (Tax Identification Number)
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('tin') is-invalid @enderror" 
                                           id="tin" 
                                           name="tin" 
                                           value="{{ old('tin') }}" 
                                           placeholder="Masukkan TIN"
                                           required>
                                    @error('tin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Tax Period Month -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tax_period_month" class="form-label required">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        Tax Period Month
                                    </label>
                                    <select class="form-select @error('tax_period_month') is-invalid @enderror" 
                                            id="tax_period_month" 
                                            name="tax_period_month" 
                                            required>
                                        <option value="">Pilih Bulan</option>
                                        @for($i = 1; $i <= 12; $i++)
                                            <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" 
                                                    {{ old('tax_period_month') == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                                {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('tax_period_month')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Tax Period Year -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tax_period_year" class="form-label required">
                                        <i class="fas fa-calendar me-1"></i>
                                        Tax Period Year
                                    </label>
                                    <select class="form-select @error('tax_period_year') is-invalid @enderror" 
                                            id="tax_period_year" 
                                            name="tax_period_year" 
                                            required>
                                        <option value="">Pilih Tahun</option>
                                        @for($year = date('Y'); $year >= 2020; $year--)
                                            <option value="{{ $year }}" 
                                                    {{ old('tax_period_year', date('Y')) == $year ? 'selected' : '' }}>
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

                        <div class="row mb-4">
                            <!-- Transaction Code -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="trx_code" class="form-label">
                                        <i class="fas fa-code me-1"></i>
                                        Transaction Code
                                    </label>
                                    <select class="form-select @error('trx_code') is-invalid @enderror" 
                                            id="trx_code" 
                                            name="trx_code">
                                        <option value="Normal" {{ old('trx_code', 'Normal') == 'Normal' ? 'selected' : '' }}>Normal</option>
                                        <option value="Replace" {{ old('trx_code') == 'Replace' ? 'selected' : '' }}>Replace</option>
                                        <option value="Cancel" {{ old('trx_code') == 'Cancel' ? 'selected' : '' }}>Cancel</option>
                                    </select>
                                    @error('trx_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Buyer Name -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="buyer_name" class="form-label">
                                        <i class="fas fa-user me-1"></i>
                                        Buyer Name
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('buyer_name') is-invalid @enderror" 
                                           id="buyer_name" 
                                           name="buyer_name" 
                                           value="{{ old('buyer_name', '-') }}" 
                                           placeholder="Nama Pembeli">
                                    @error('buyer_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Buyer ID Opt -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="buyer_id_opt" class="form-label">
                                        <i class="fas fa-id-badge me-1"></i>
                                        Buyer ID Option
                                    </label>
                                    <select class="form-select @error('buyer_id_opt') is-invalid @enderror" 
                                            id="buyer_id_opt" 
                                            name="buyer_id_opt">
                                        <option value="NIK" {{ old('buyer_id_opt', 'NIK') == 'NIK' ? 'selected' : '' }}>NIK</option>
                                        <option value="NPWP" {{ old('buyer_id_opt') == 'NPWP' ? 'selected' : '' }}>NPWP</option>
                                        <option value="PASSPORT" {{ old('buyer_id_opt') == 'PASSPORT' ? 'selected' : '' }}>PASSPORT</option>
                                    </select>
                                    @error('buyer_id_opt')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <!-- Buyer ID Number -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="buyer_id_number" class="form-label">
                                        <i class="fas fa-hashtag me-1"></i>
                                        Buyer ID Number
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('buyer_id_number') is-invalid @enderror" 
                                           id="buyer_id_number" 
                                           name="buyer_id_number" 
                                           value="{{ old('buyer_id_number') }}" 
                                           placeholder="16 digit number"
                                           maxlength="16">
                                    @error('buyer_id_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Good Service Opt -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="good_service_opt" class="form-label">
                                        <i class="fas fa-box me-1"></i>
                                        Good Service Option
                                    </label>
                                    <select class="form-select @error('good_service_opt') is-invalid @enderror" 
                                            id="good_service_opt" 
                                            name="good_service_opt">
                                        <option value="A" {{ old('good_service_opt', 'A') == 'A' ? 'selected' : '' }}>A</option>
                                        <option value="B" {{ old('good_service_opt') == 'B' ? 'selected' : '' }}>B</option>
                                        <option value="C" {{ old('good_service_opt') == 'C' ? 'selected' : '' }}>C</option>
                                    </select>
                                    @error('good_service_opt')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Serial No -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="serial_no" class="form-label">
                                        <i class="fas fa-barcode me-1"></i>
                                        Serial Number
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('serial_no') is-invalid @enderror" 
                                           id="serial_no" 
                                           name="serial_no" 
                                           value="{{ old('serial_no', '-') }}" 
                                           placeholder="Serial Number">
                                    @error('serial_no')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Advanced Options -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-cog me-2"></i>
                                    Advanced Options
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Transaction Date -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="transaction_date" class="form-label">
                                                <i class="fas fa-calendar-day me-1"></i>
                                                Transaction Date
                                            </label>
                                            <input type="date" 
                                                   class="form-control @error('transaction_date') is-invalid @enderror" 
                                                   id="transaction_date" 
                                                   name="transaction_date" 
                                                   value="{{ old('transaction_date', date('Y-m-d')) }}">
                                            @error('transaction_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Tax Base Selling Price -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="tax_base_selling_price" class="form-label">
                                                <i class="fas fa-dollar-sign me-1"></i>
                                                Tax Base Selling Price
                                            </label>
                                            <input type="number" 
                                                   class="form-control @error('tax_base_selling_price') is-invalid @enderror" 
                                                   id="tax_base_selling_price" 
                                                   name="tax_base_selling_price" 
                                                   value="{{ old('tax_base_selling_price', 0) }}" 
                                                   step="0.01"
                                                   min="0">
                                            @error('tax_base_selling_price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Other Tax Selling Price -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="other_tax_selling_price" class="form-label">
                                                <i class="fas fa-percentage me-1"></i>
                                                Other Tax Selling Price
                                            </label>
                                            <input type="number" 
                                                   class="form-control @error('other_tax_selling_price') is-invalid @enderror" 
                                                   id="other_tax_selling_price" 
                                                   name="other_tax_selling_price" 
                                                   value="{{ old('other_tax_selling_price', 0) }}" 
                                                   step="0.01"
                                                   min="0">
                                            @error('other_tax_selling_price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- VAT -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="vat" class="form-label">
                                                <i class="fas fa-receipt me-1"></i>
                                                VAT (Value Added Tax)
                                            </label>
                                            <input type="number" 
                                                   class="form-control @error('vat') is-invalid @enderror" 
                                                   id="vat" 
                                                   name="vat" 
                                                   value="{{ old('vat', 0) }}" 
                                                   step="0.01"
                                                   min="0">
                                            <small class="form-text text-muted">
                                                Formula: round(TaxBaseSellingPrice * 11/12) dibulatkan ke satuan (1-4 down, 5-9 up)
                                            </small>
                                            @error('vat')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- STLG -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="stlg" class="form-label">
                                                <i class="fas fa-stamp me-1"></i>
                                                STLG
                                            </label>
                                            <select class="form-select @error('stlg') is-invalid @enderror" 
                                                    id="stlg" 
                                                    name="stlg">
                                                <option value="0" {{ old('stlg', '0') == '0' ? 'selected' : '' }}>0</option>
                                                <option value="1" {{ old('stlg') == '1' ? 'selected' : '' }}>1</option>
                                            </select>
                                            @error('stlg')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Info -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="info" class="form-label">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Additional Info
                                            </label>
                                            <input type="text" 
                                                   class="form-control @error('info') is-invalid @enderror" 
                                                   id="info" 
                                                   name="info" 
                                                   value="{{ old('info', 'ok') }}" 
                                                   placeholder="Additional Information">
                                            @error('info')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Export Actions -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="validate_before_export" name="validate_before_export" checked>
                                        <label class="form-check-label" for="validate_before_export">
                                            <i class="fas fa-shield-alt me-1"></i>
                                            Validate data before export
                                        </label>
                                    </div>
                                    
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                            <i class="fas fa-undo me-1"></i>
                                            Reset
                                        </button>
                                        <button type="button" class="btn btn-info" onclick="previewXML()">
                                            <i class="fas fa-eye me-1"></i>
                                            Preview
                                        </button>
                                        <button type="submit" class="btn btn-success" id="exportBtn">
                                            <i class="fas fa-download me-1"></i>
                                            Export XML
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
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto calculate VAT when tax base selling price changes
    document.getElementById('tax_base_selling_price').addEventListener('input', function() {
        const taxBase = parseFloat(this.value) || 0;
        const vat = Math.round((taxBase * 11) / 12);
        document.getElementById('vat').value = vat;
    });

    // Form validation
    document.getElementById('xmlExportForm').addEventListener('submit', function(e) {
        const validateBeforeExport = document.getElementById('validate_before_export').checked;
        
        if (validateBeforeExport) {
            if (!validateForm()) {
                e.preventDefault();
                return false;
            }
        }
        
        // Show loading state
        const exportBtn = document.getElementById('exportBtn');
        exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Exporting...';
        exportBtn.disabled = true;
    });
});

function validateForm() {
    let isValid = true;
    const errors = [];

    // Validate TIN
    const tin = document.getElementById('tin').value.trim();
    if (!tin) {
        errors.push('TIN is required');
        isValid = false;
    }

    // Validate Tax Period
    const month = document.getElementById('tax_period_month').value;
    const year = document.getElementById('tax_period_year').value;
    
    if (!month || !year) {
        errors.push('Tax period (month and year) is required');
        isValid = false;
    }

    // Validate Buyer ID Number length
    const buyerIdNumber = document.getElementById('buyer_id_number').value;
    if (buyerIdNumber && buyerIdNumber.length !== 16) {
        errors.push('Buyer ID Number must be exactly 16 digits');
        isValid = false;
    }

    if (!isValid) {
        alert('Validation Errors:\n' + errors.join('\n'));
    }

    return isValid;
}

function resetForm() {
    if (confirm('Are you sure you want to reset all form data?')) {
        document.getElementById('xmlExportForm').reset();
        // Reset to default values
        document.getElementById('trx_code').value = 'Normal';
        document.getElementById('buyer_name').value = '-';
        document.getElementById('buyer_id_opt').value = 'NIK';
        document.getElementById('good_service_opt').value = 'A';
        document.getElementById('serial_no').value = '-';
        document.getElementById('info').value = 'ok';
        document.getElementById('transaction_date').value = new Date().toISOString().split('T')[0];
        document.getElementById('tax_base_selling_price').value = 0;
        document.getElementById('other_tax_selling_price').value = 0;
        document.getElementById('vat').value = 0;
        document.getElementById('stlg').value = '0';
    }
}

function previewXML() {
    const formData = new FormData(document.getElementById('xmlExportForm'));
    
    fetch('{{ route("laporan.preview.xml") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.text())
    .then(xmlContent => {
        document.getElementById('xmlPreviewContent').textContent = xmlContent;
        new bootstrap.Modal(document.getElementById('previewModal')).show();
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

@push('styles')
<style>
.required::after {
    content: " *";
    color: red;
}

.form-group {
    margin-bottom: 1rem;
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

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.form-check-label {
    font-size: 0.9rem;
}

.invalid-feedback {
    display: block;
}
</style>
@endpush
@endsection