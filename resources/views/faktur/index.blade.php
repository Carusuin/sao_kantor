@extends('dashboard_layout')

@section('title', 'Laporan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-file-alt mr-2"></i>
                        Daftar Laporan
                    </h3>
                    <a href="{{ route('laporan.create') }}" class="btn btn-warning">
                        <i class="fas fa-plus mr-1"></i>
                        Tambah Laporan
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
                                        <th>TIN</th>
                                        <th>Periode</th>
                                        <th>Tanggal Transaksi</th>
                                        <th>Tax Base Selling Price</th>
                                        <th>Other Tax Selling Price</th>
                                        <th>VAT</th>
                                        <th>Dibuat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($laporans as $index => $laporan)
                                        <tr>
                                            <td>{{ $laporans->firstItem() + $index }}</td>
                                            <td>{{ $laporan->tin }}</td>
                                            <td>{{ str_pad($laporan->tax_period_month, 2, '0', STR_PAD_LEFT) }}/{{ $laporan->tax_period_year }}</td>
                                            <td>{{ $laporan->transaction_date->format('d-m-Y') }}</td>
                                            <td class="text-right">Rp {{ number_format($laporan->tax_base_selling_price, 0, ',', '.') }}</td>
                                            <td class="text-right">Rp {{ number_format($laporan->other_tax_selling_price, 0, ',', '.') }}</td>
                                            <td class="text-right">Rp {{ number_format($laporan->vat, 0, ',', '.') }}</td>
                                            <td>{{ $laporan->created_at->format('d-m-Y H:i') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('laporan.show', $laporan) }}" class="btn btn-info btn-sm" title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('laporan.edit', $laporan) }}" class="btn btn-warning btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('laporan.export.xml', $laporan) }}" class="btn btn-success btn-sm" title="Download XML">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <a href="{{ route('laporan.preview.xml', $laporan) }}" class="btn btn-secondary btn-sm" target="_blank" title="Preview XML">
                                                        <i class="fas fa-code"></i>
                                                    </a>
                                                    <form action="{{ route('laporan.destroy', $laporan) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus laporan ini?')">
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

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $laporans->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada laporan</h5>
                            <p class="text-muted">Klik tombol "Tambah Laporan" untuk membuat laporan baru</p>
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