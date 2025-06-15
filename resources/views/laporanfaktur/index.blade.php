@extends('dashboard_layout')

@section('title', 'Laporan E-Faktur')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-file-alt mr-2"></i>
                        Daftar Laporan E-Faktur ANJAYYYYYY
                    </h3>
                    <a href="{{ route('laporan_faktur.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i>
                        Tambah Laporan E-Faktur
                    </a>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif

                    @if($laporans->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Baris</th>
                                        <th>Jenis</th>
                                        <th>Kode</th>
                                        <th>Nama Barang/Jasa</th>
                                        <th>Satuan</th>
                                        <th>Harga Satuan</th>
                                        <th>Jumlah</th>
                                        <th>DPP</th>
                                        <th>PPN</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($laporans as $index => $laporan)
                                        <tr>
                                            <td>{{ $laporans->firstItem() + $index }}</td>
                                            <td>{{ $laporan->baris }}</td>
                                            <td>{{ $laporan->jenis_barang_jasa_text }}</td>
                                            <td>{{ $laporan->kode_barang_jasa }}</td>
                                            <td>{{ $laporan->nama_barang_jasa }}</td>
                                            <td>{{ $laporan->nama_satuan_ukur }}</td>
                                            <td class="text-right">{{ $laporan->formatted_harga_satuan }}</td>
                                            <td class="text-right">{{ number_format($laporan->jumlah_barang_jasa, 2, ',', '.') }}</td>
                                            <td class="text-right">Rp {{ number_format($laporan->dpp, 0, ',', '.') }}</td>
                                            <td class="text-right">Rp {{ number_format($laporan->ppn, 0, ',', '.') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('laporan_faktur.show', $laporan) }}" class="btn btn-info btn-sm" title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('laporan_faktur.edit', $laporan) }}" class="btn btn-warning btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('laporan_faktur.export.xml', $laporan) }}" class="btn btn-success btn-sm" title="Download XML">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <a href="{{ route('laporan_faktur.preview.xml', $laporan) }}" class="btn btn-secondary btn-sm" target="_blank" title="Preview XML">
                                                        <i class="fas fa-code"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $laporans->links() }}
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
@endsection

@push('styles')
<style>
    .table th {
        vertical-align: middle;
        white-space: nowrap;
    }
    
    .btn-group .btn {
        margin-right: 2px;
    }
    
    .btn-group .btn:last-child {
        margin-right: 0;
    }
</style>
@endpush 