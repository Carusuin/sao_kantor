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
                        Daftar Laporan E-Faktur
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

                    @if($fakturs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th class="text-center">
                                            <input type="checkbox" id="checkAll">
                                        </th>
                                        <th>NPWP Pembeli / Identitas lainnya</th>
                                        <th>Nama Pembeli</th>
                                        <th>Kode Transaksi <i class="fas fa-sort"></i></th>
                                        <th>Nomor Faktur Pajak</th>
                                        <th>Tanggal Faktur Pajak <i class="fas fa-sort"></i></th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($fakturs as $index => $faktur)
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" class="row-check">
                                            </td>
                                            <td>{{ $faktur->npwp_nik_pembeli }}</td>
                                            <td>{{ $faktur->nama_pembeli }}</td>
                                            <td>{{ $faktur->kode_transaksi }}</td>
                                            <td>{{ $faktur->id }}</td>
                                            <td>{{ $faktur->tanggal_faktur->format('d-m-Y') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('laporan_faktur.show', $faktur->id) }}" class="btn btn-info btn-sm" title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('laporan_faktur.edit', $faktur->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('laporan_faktur.export.xml', $faktur->id) }}" class="btn btn-success btn-sm" title="Download XML">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <a href="{{ route('laporan_faktur.preview.xml', $faktur->id) }}" class="btn btn-secondary btn-sm" target="_blank" title="Preview XML">
                                                        <i class="fas fa-code"></i>
                                                    </a>
                                                    <form action="{{ route('laporan_faktur.destroy', $faktur->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus faktur ini?\nIni juga akan menghapus semua detail terkait.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
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