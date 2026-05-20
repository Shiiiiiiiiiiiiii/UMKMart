@extends('layouts.app')

@section('title', 'Login - UMKMart')

@section('content')
<div class="auth-container">
    <div class="auth-card fade-in">
        <div class="text-center mb-4">
            <h2 class="fw-bold"><i class="bi bi-person-circle me-2 text-primary"></i>Login</h2>
            <p class="text-secondary small">Masuk ke akun UMKMart Anda</p>
        </div>

        <div id="loginAlert" class="alert alert-danger d-none" style="font-size:0.875rem;"></div>

        <form id="loginForm">
            <div class="mb-3">
                <label class="form-label form-label-custom">Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark-2 border-end-0 text-secondary" style="background:var(--dark-2);border-color:var(--border);"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control form-control-dark border-start-0 ps-0" id="email" required placeholder="nama@email.com">
                </div>
            </div>
            
            <div class="mb-4">
                <label class="form-label form-label-custom">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark-2 border-end-0 text-secondary" style="background:var(--dark-2);border-color:var(--border);"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control form-control-dark border-start-0 ps-0" id="password" required placeholder="••••••••">
                </div>
            </div>

            <button type="submit" class="btn btn-primary-custom w-100 mb-3" id="btnLogin">
                <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
            </button>
        </form>

        <div class="text-center mt-3">
            <span class="text-secondary small">Belum punya akun? <a href="{{ route('register') }}" class="text-primary text-decoration-none">Daftar sekarang</a></span>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('loginForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const btn = document.getElementById('btnLogin');
        const alertBox = document.getElementById('loginAlert');
        
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Loading...';
        alertBox.classList.add('d-none');

        try {
            const res = await apiRequest('/login', 'POST', { email, password });
            
            if (res.success) {
                setAuth(res.data.token, res.data.user);
                
                // Redirect based on role
                if (res.data.user.role === 'seller') {
                    window.location.href = '/dashboard/seller';
                } else if (res.data.user.role === 'buyer') {
                    window.location.href = '/dashboard/buyer';
                } else {
                    window.location.href = '/';
                }
            } else {
                alertBox.textContent = res.message || 'Login gagal. Periksa kembali email dan password Anda.';
                alertBox.classList.remove('d-none');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-box-arrow-in-right me-2"></i>Masuk';
            }
        } catch (error) {
            alertBox.textContent = 'Terjadi kesalahan jaringan.';
            alertBox.classList.remove('d-none');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-box-arrow-in-right me-2"></i>Masuk';
        }
    });
</script>
@endsection
