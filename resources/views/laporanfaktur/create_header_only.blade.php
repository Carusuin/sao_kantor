@extends('dashboard_layout')

@section('title', 'Buat Header Faktur Pajak Keluaran')
@section('page-title', 'Buat Header Faktur Pajak Keluaran')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-file-invoice me-2"></i>Buat Header Faktur Pajak Keluaran Baru
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('laporan_faktur.store_header') }}" method="POST" id="createHeaderFakturForm">
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
                                        <input type="text" class="form-control" id="npwpPenjual" name="npwp_penjual" placeholder="0013575832046000" required maxlength="16" minlength="16" pattern="[0-9]{16}" title="NPWP harus 16 digit angka">
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
                                        <input type="text" class="form-control" id="npwpPembeli" name="npwp_pembeli" placeholder="0013575832046000" required maxlength="16" minlength="16" pattern="[0-9]{16}" title="NPWP harus 16 digit angka">
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
@endsection

@push('scripts')
<script>
$(document).ready(function() {
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
});
</script>
@endpush 