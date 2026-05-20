@extends('layouts.app')

@section('title', 'Daftar - UMKMart')

@section('content')
<div class="auth-container py-5">
    <div class="auth-card fade-in" style="max-width:500px;">
        <div class="text-center mb-4">
            <h2 class="fw-bold"><i class="bi bi-person-plus me-2 text-secondary"></i>Daftar Akun</h2>
            <p class="text-secondary small">Bergabung dengan komunitas UMKMart</p>
        </div>

        <div id="registerAlert" class="alert d-none" style="font-size:0.875rem;"></div>

        <form id="registerForm">
            <div class="mb-3">
                <label class="form-label form-label-custom">Nama Lengkap</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark-2 border-end-0 text-secondary" style="background:var(--dark-2);border-color:var(--border);"><i class="bi bi-person"></i></span>
                    <input type="text" class="form-control form-control-dark border-start-0 ps-0" id="name" required placeholder="Nama Anda">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label form-label-custom">Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark-2 border-end-0 text-secondary" style="background:var(--dark-2);border-color:var(--border);"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control form-control-dark border-start-0 ps-0" id="email" required placeholder="nama@email.com">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label form-label-custom">Password</label>
                    <input type="password" class="form-control form-control-dark" id="password" required placeholder="••••••••" minlength="6">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label form-label-custom">Konfirmasi</label>
                    <input type="password" class="form-control form-control-dark" id="password_confirmation" required placeholder="••••••••">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label form-label-custom">Mendaftar Sebagai</label>
                <div class="d-flex gap-3">
                    <div class="form-check flex-fill p-0">
                        <input type="radio" class="btn-check" name="role" id="role_buyer" value="buyer" autocomplete="off" checked>
                        <label class="btn btn-outline-custom w-100 py-2 d-flex flex-column align-items-center justify-content-center" for="role_buyer" style="border-radius:12px;">
                            <i class="bi bi-bag mb-1 fs-5"></i>
                            <span style="font-size:0.85rem;">Pembeli (Buyer)</span>
                        </label>
                    </div>
                    <div class="form-check flex-fill p-0">
                        <input type="radio" class="btn-check" name="role" id="role_seller" value="seller" autocomplete="off">
                        <label class="btn btn-outline-custom w-100 py-2 d-flex flex-column align-items-center justify-content-center" for="role_seller" style="border-radius:12px;border-color:var(--secondary);color:var(--secondary);">
                            <i class="bi bi-shop mb-1 fs-5"></i>
                            <span style="font-size:0.85rem;">Penjual (Seller)</span>
                        </label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary-custom w-100 mb-3" id="btnRegister">
                <i class="bi bi-person-plus me-2"></i>Daftar Sekarang
            </button>
        </form>

        <div class="text-center mt-3">
            <span class="text-secondary small">Sudah punya akun? <a href="{{ route('login') }}" class="text-primary text-decoration-none">Masuk di sini</a></span>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Styling helper for radio buttons
    document.querySelectorAll('input[name="role"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if(this.value === 'seller') {
                document.querySelector('label[for="role_seller"]').style.background = 'var(--secondary)';
                document.querySelector('label[for="role_seller"]').style.color = '#fff';
                document.querySelector('label[for="role_buyer"]').style.background = 'transparent';
                document.querySelector('label[for="role_buyer"]').style.color = 'var(--primary-light)';
            } else {
                document.querySelector('label[for="role_buyer"]').style.background = 'var(--primary)';
                document.querySelector('label[for="role_buyer"]').style.color = '#fff';
                document.querySelector('label[for="role_seller"]').style.background = 'transparent';
                document.querySelector('label[for="role_seller"]').style.color = 'var(--secondary)';
            }
        });
    });

    document.getElementById('registerForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const name = document.getElementById('name').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const password_confirmation = document.getElementById('password_confirmation').value;
        const role = document.querySelector('input[name="role"]:checked').value;
        
        const btn = document.getElementById('btnRegister');
        const alertBox = document.getElementById('registerAlert');
        
        if (password !== password_confirmation) {
            alertBox.textContent = 'Password dan Konfirmasi Password tidak cocok.';
            alertBox.className = 'alert alert-danger';
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Loading...';
        alertBox.classList.add('d-none');

        try {
            const res = await apiRequest('/register', 'POST', { name, email, password, password_confirmation, role });
            
            if (res.success) {
                alertBox.textContent = 'Registrasi berhasil! Mengalihkan...';
                alertBox.className = 'alert alert-success';
                
                setAuth(res.data.token, res.data.user);
                
                setTimeout(() => {
                    if (res.data.user.role === 'seller') {
                        window.location.href = '/dashboard/seller';
                    } else {
                        window.location.href = '/dashboard/buyer';
                    }
                }, 1000);
            } else {
                let errorMsg = res.message;
                if (res.data) {
                    const errors = Object.values(res.data).flat().join('<br>');
                    if (errors) errorMsg = errors;
                }
                alertBox.innerHTML = errorMsg;
                alertBox.className = 'alert alert-danger';
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-person-plus me-2"></i>Daftar Sekarang';
            }
        } catch (error) {
            alertBox.textContent = 'Terjadi kesalahan jaringan.';
            alertBox.className = 'alert alert-danger';
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-person-plus me-2"></i>Daftar Sekarang';
        }
    });
</script>
@endsection
