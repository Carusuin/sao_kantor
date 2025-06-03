@extends('dashboard_layout')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row">
    <!-- Welcome Card -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="mb-2">Selamat Datang, {{ $user->name }}! 👋</h4>
                        <p class="text-muted mb-0">
                            <strong>Jabatan:</strong> {{ $user->jabatan ?? 'Tidak tersedia' }} | 
                            <strong>Divisi:</strong> {{ $user->divisi ?? 'Tidak tersedia' }} |
                            <strong>Role:</strong> <span class="badge bg-primary">{{ ucfirst($user->role) }}</span>
                        </p>
                        <small class="text-muted">Terakhir login: {{ now()->format('d M Y, H:i') }} WIB</small>
                    </div>
                    <div class="col-md-4 text-end">
                        <i class="fas fa-user-tie fa-4x text-primary opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="col-md-3 mb-4">
        <div class="stats-card success">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-uppercase mb-2">Total Users</h6>
                    <h2 class="mb-0">{{ $stats['total_users'] }}</h2>
                    <small>Semua pengguna sistem</small>
                </div>
                <div class="align-self-center">
                    <i class="fas fa-users fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="stats-card warning">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-uppercase mb-2">Users Aktif</h6>
                    <h2 class="mb-0">{{ $stats['active_users'] }}</h2>
                    <small>Pengguna yang aktif</small>
                </div>
                <div class="align-self-center">
                    <i class="fas fa-user-check fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="stats-card info">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-uppercase mb-2">Administrator</h6>
                    <h2 class="mb-0">{{ $stats['admin_count'] }}</h2>
                    <small>Total admin sistem</small>
                </div>
                <div class="align-self-center">
                    <i class="fas fa-user-shield fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="stats-card danger">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-uppercase mb-2">Staff</h6>
                    <h2 class="mb-0">{{ $stats['staff_count'] }}</h2>
                    <small>Total staff kantor</small>
                </div>
                <div class="align-self-center">
                    <i class="fas fa-user-tie fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Chart Card -->
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    Statistik Bulanan
                </h5>
            </div>
            <div class="card-body">
                <canvas id="monthlyChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>
                    Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-plus me-2"></i>Tambah User Baru
                    </button>
                    <button class="btn btn-outline-success btn-sm">
                        <i class="fas fa-file-alt me-2"></i>Generate Laporan
                    </button>
                    <button class="btn btn-outline-info btn-sm">
                        <i class="fas fa-calendar me-2"></i>Lihat Jadwal
                    </button>
                    <button class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-cog me-2"></i>Pengaturan Sistem
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Activities -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-clock me-2"></i>
                    Aktivitas Terbaru
                </h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">User baru terdaftar</h6>
                            <p class="text-muted mb-1">John Doe bergabung sebagai staff administrasi</p>
                            <small class="text-muted">2 jam yang lalu</small>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-info"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Laporan dibuat</h6>
                            <p class="text-muted mb-1">Laporan keuangan bulan ini telah selesai</p>
                            <small class="text-muted">5 jam yang lalu</small>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-warning"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Sistem update</h6>
                            <p class="text-muted mb-1">Pembaruan keamanan sistem berhasil</p>
                            <small class="text-muted">1 hari yang lalu</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Info -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Informasi Sistem
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="text-center p-3 border rounded mb-3">
                            <i class="fas fa-server fa-2x text-primary mb-2"></i>
                            <h6>Server Status</h6>
                            <span class="badge bg-success">Online</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-3 border rounded mb-3">
                            <i class="fas fa-database fa-2x text-success mb-2"></i>
                            <h6>Database</h6>
                            <span class="badge bg-success">Connected</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-3 border rounded">
                            <i class="fas fa-shield-alt fa-2x text-warning mb-2"></i>
                            <h6>Security</h6>
                            <span class="badge bg-warning">Protected</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-3 border rounded">
                            <i class="fas fa-cloud fa-2x text-info mb-2"></i>
                            <h6>Backup</h6>
                            <span class="badge bg-info">Daily</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 30px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #dee2e6;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 2rem;
    }

    .timeline-marker {
        position: absolute;
        left: -23px;
        top: 5px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 3px solid white;
        box-shadow: 0 0 0 3px #dee2e6;
    }

    .timeline-content h6 {
        font-weight: 600;
        color: #495057;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Chart
    const ctx = document.getElementById('monthlyChart').getContext('2d');
    const monthlyChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($monthlyData['labels']),
            datasets: [{
                label: 'Aktivitas',
                data: @json($monthlyData['data']),
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#667eea',
                pointBorderColor: '#fff',
                pointBorderWidth: 3,
                pointRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            elements: {
                point: {
                    hoverRadius: 8
                }
            }
        }
    });
});
</script>
@endpush