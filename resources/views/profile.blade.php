@extends('layouts.dashboard')

@section('title', 'Profile')
@section('page-title', 'Profile Pengguna')

@section('content')
<div class="row">
    <div class="col-md-4">
        <!-- Profile Card -->
        <div class="card">
            <div class="card-body text-center">
                <div class="avatar-wrapper mb-3">
                    <div class="avatar bg-primary text-white d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 100px; height: 100px; font-size: 2rem;">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                </div>
                <h4 class="mb-1">{{ $user->name }}</h4>
                <p class="text-muted mb-2">{{ $user->email }}</p>
                <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'manager' ? 'warning' : 'primary') }} mb-3">
                    {{ ucfirst($user->role) }}
                </span>
                
                <div class="row text-center mt-4">
                    <div class="col-6">
                        <div class="p-2 border rounded">
                            <h6 class="mb-1">Status</h6>
                            <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 border rounded">
                            <h6 class="mb-1">Bergabung</h6>
                            <small class="text-muted">{{ $user->created_at->format('M Y') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    Statistik Singkat
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span>Login terakhir:</span>
                    <strong>{{ now()->format('d M Y') }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Total login:</span>
                    <strong>{{ rand(50, 200) }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Akun dibuat:</span>
                    <strong>{{ $user->created_at->diffForHumans() }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <!-- Profile Edit Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-user-edit me-2"></i>
                    Edit Profile
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $user->name) }}"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $user->email) }}"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="jabatan" class="form-label">Jabatan</label>
                            <input type="text" 
                                   class="form-control @error('jabatan') is-invalid @enderror" 
                                   id="jabatan" 
                                   name="jabatan" 
                                   value="{{ old('jabatan', $user->jabatan) }}">
                            @error('jabatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="divisi" class="form-label">Divisi</label>
                            <select class="form-select @error('divisi') is-invalid @enderror" 
                                    id="divisi" 
                                    name="divisi">
                                <option value="">Pilih Divisi</option>
                                <option value="IT" {{ old('divisi', $user->divisi) === 'IT' ? 'selected' : '' }}>IT</option>
                                <option value="Human Resource" {{ old('divisi', $user->divisi) === 'Human Resource' ? 'selected' : '' }}>Human Resource</option>
                                <option value="Keuangan" {{ old('divisi', $user->divisi) === 'Keuangan' ? 'selected' : '' }}>Keuangan</option>
                                <option value="Administrasi" {{ old('divisi', $user->divisi) === 'Administrasi' ? 'selected' : '' }}>Administrasi</option>
                                <option value="Umum" {{ old('divisi', $user->divisi) === 'Umum' ? 'selected' : '' }}>Umum</option>
                                <option value="Marketing" {{ old('divisi', $user->divisi) === 'Marketing' ? 'selected' : '' }}>Marketing</option>
                                <option value="Operasional" {{ old('divisi', $user->divisi) === 'Operasional' ? 'selected' : '' }}>Operasional</option>
                            </select>
                            @error('divisi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 mb-3">
                            <label for="role" class="form-label">Role</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="role" 
                                   value="{{ ucfirst($user->role) }}"
                                   readonly>
                            <small class="text-muted">Role hanya dapat diubah oleh Administrator</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                            <i class="fas fa-undo me-2"></i>Reset
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Change Password Card -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-lock me-2"></i>
                    Ubah Password
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('password.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="current_password" class="form-label">Password Saat Ini</label>
                            <input type="password" 
                                   class="form-control @error('current_password') is-invalid @enderror" 
                                   id="current_password" 
                                   name="current_password"
                                   required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password Baru</label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password"
                                   required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password_confirmation" 
                                   name="password_confirmation"
                                   required>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-key me-2"></i>Ubah Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function resetForm() {
    if (confirm('Apakah Anda yakin ingin mereset form?')) {
        document.querySelector('form').reset();
    }
}

// Show/Hide password
document.addEventListener('DOMContentLoaded', function() {
    const passwordInputs = document.querySelectorAll('input[type="password"]');
    
    passwordInputs.forEach(function(input) {
        const wrapper = document.createElement('div');
        wrapper.className = 'input-group';
        
        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(input);
        
        const button = document.createElement('button');
        button.className = 'btn btn-outline-secondary';
        button.type = 'button';
        button.innerHTML = '<i class="fas fa-eye"></i>';
        
        wrapper.appendChild(button);
        
        button.addEventListener('click', function() {
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            
            const icon = button.querySelector('i');
            icon.className = type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
        });
    });
});
</script>
@endpush