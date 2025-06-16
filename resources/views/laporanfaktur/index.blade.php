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
                                        <th>ID</th>
                                        <th>NPWP Pembeli / Identitas lainnya</th>
                                        <th>Nama Pembeli</th>
                                        <th>Kode Transaksi</th>
                                        <th>Nomor Faktur Pajak</th>
                                        <th>Tanggal Faktur Pajak</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($fakturs as $index => $faktur)
                                        <tr>
                                            <td>{{ $faktur->id }}</td>
                                            <td>{{ $faktur->npwp_nik_pembeli }}</td>
                                            <td>{{ $faktur->nama_pembeli }}</td>
                                            <td>{{ $faktur->kode_transaksi }}</td>
                                            <td>{{ $faktur->nomor_faktur }}</td>
                                            <td>{{ $faktur->tanggal_faktur->format('d-m-Y') }}</td>
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
</style>
@endpush 