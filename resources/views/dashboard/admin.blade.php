@extends('layouts.app')

@section('title', 'Dashboard Admin - UMKMart')

@section('content')
<div class="container py-5" id="app-container" style="display:none;">
    <div class="d-flex justify-content-between align-items-center mb-4 fade-in">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-shield-lock me-2 text-primary-light"></i>Dashboard Admin</h2>
            <p class="text-secondary mb-0">Kelola dan pantau seluruh operasional UMKM lokal UMKMart.</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4 fade-in">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value" id="statShopsTotal">0</div>
                        <div class="stat-label">Total Toko UMKM</div>
                    </div>
                    <div class="stat-icon bg-primary bg-opacity-20 text-primary-light">
                        <i class="bi bi-shop"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value" id="statShopsPending">0</div>
                        <div class="stat-label">Toko Menunggu Persetujuan</div>
                    </div>
                    <div class="stat-icon bg-warning bg-opacity-20 text-warning">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value" id="statOrdersTotal">0</div>
                        <div class="stat-label">Total Transaksi Platform</div>
                    </div>
                    <div class="stat-icon bg-success bg-opacity-20 text-success">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Content -->
    <div class="fade-in">
        <ul class="nav nav-pills mb-4" id="adminTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-shops" type="button">
                    <i class="bi bi-shop me-2"></i>Persetujuan Toko
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-orders" type="button">
                    <i class="bi bi-receipt me-2"></i>Semua Pesanan
                </button>
            </li>
        </ul>

        <div class="tab-content" id="adminTabsContent">
            <!-- Shops Tab -->
            <div class="tab-pane fade show active" id="tab-shops" role="tabpanel">
                <div class="glass-card">
                    <h5 class="fw-bold mb-3">Manajemen Toko</h5>
                    <div class="table-responsive">
                        <table class="table table-dark-custom mb-0">
                            <thead>
                                <tr>
                                    <th>Info Toko</th>
                                    <th>Pemilik (Seller)</th>
                                    <th>Alamat</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="adminShopsBody">
                                <tr><td colspan="5" class="text-center py-4"><span class="spinner-border spinner-border-sm me-2"></span>Memuat data...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Orders Tab -->
            <div class="tab-pane fade" id="tab-orders" role="tabpanel">
                <div class="glass-card">
                    <h5 class="fw-bold mb-3">Riwayat Seluruh Transaksi</h5>
                    <div class="table-responsive">
                        <table class="table table-dark-custom mb-0">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Toko</th>
                                    <th>Pembeli</th>
                                    <th>Total Tagihan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="adminOrdersBody">
                                <tr><td colspan="5" class="text-center py-4"><span class="spinner-border spinner-border-sm me-2"></span>Memuat data...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
    .nav-pills .nav-link { color: var(--text-secondary); padding: 0.5rem 1.5rem; border-radius: 20px; font-weight: 600; }
    .nav-pills .nav-link.active { background: var(--primary); color: white; }
</style>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const user = getUser();
        if (!user || user.role !== 'admin') {
            window.location.href = '/login';
            return;
        }

        document.getElementById('app-container').style.display = 'block';
        loadAdminData();
    });

    async function loadAdminData() {
        await Promise.all([
            loadShops(),
            loadOrders()
        ]);
    }

    async function loadShops() {
        const res = await apiRequest('/admin/shops');
        const tbody = document.getElementById('adminShopsBody');
        
        if (res.success && res.data.length > 0) {
            let html = '';
            let totalShops = res.data.length;
            let pendingShops = 0;

            res.data.forEach(shop => {
                let statusBadge = '';
                let actions = '';

                if (shop.status === 'pending') {
                    pendingShops++;
                    statusBadge = '<span class="status-badge status-pending">Pending</span>';
                    actions = `
                        <button class="btn btn-sm btn-success me-1 px-3" onclick="updateShopStatus(${shop.id}, 'active')">
                            <i class="bi bi-check-lg me-1"></i>Setujui
                        </button>
                        <button class="btn btn-sm btn-outline-danger px-3" onclick="updateShopStatus(${shop.id}, 'banned')">
                            <i class="bi bi-slash-circle me-1"></i>Blokir
                        </button>
                    `;
                } else if (shop.status === 'active') {
                    statusBadge = '<span class="status-badge status-active">Aktif</span>';
                    actions = `
                        <button class="btn btn-sm btn-outline-danger px-3" onclick="updateShopStatus(${shop.id}, 'banned')">
                            <i class="bi bi-slash-circle me-1"></i>Blokir
                        </button>
                    `;
                } else {
                    statusBadge = '<span class="status-badge status-cancelled">Diblokir</span>';
                    actions = `
                        <button class="btn btn-sm btn-outline-success px-3" onclick="updateShopStatus(${shop.id}, 'active')">
                            <i class="bi bi-check-lg me-1"></i>Aktifkan Kembali
                        </button>
                    `;
                }

                html += `
                    <tr>
                        <td>
                            <div class="fw-bold">${shop.name}</div>
                            <small class="text-muted">${shop.description || 'Tidak ada deskripsi'}</small>
                        </td>
                        <td>${shop.user ? shop.user.name : '-'} <br><small class="text-muted">${shop.user ? shop.user.email : ''}</small></td>
                        <td><small>${shop.address || '-'}</small></td>
                        <td>${statusBadge}</td>
                        <td>${actions}</td>
                    </tr>
                `;
            });

            document.getElementById('statShopsTotal').textContent = totalShops;
            document.getElementById('statShopsPending').textContent = pendingShops;
            tbody.innerHTML = html;
        } else {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">Belum ada toko yang terdaftar.</td></tr>';
        }
    }

    async function loadOrders() {
        const res = await apiRequest('/admin/orders');
        const tbody = document.getElementById('adminOrdersBody');
        
        if (res.success && res.data.length > 0) {
            let html = '';
            document.getElementById('statOrdersTotal').textContent = res.data.length;

            res.data.forEach(order => {
                let statusBadge = '';
                switch(order.status) {
                    case 'pending': statusBadge = '<span class="status-badge status-pending">Pending</span>'; break;
                    case 'waiting_payment': statusBadge = '<span class="status-badge status-waiting_payment">Menunggu Pembayaran</span>'; break;
                    case 'paid': statusBadge = '<span class="status-badge status-active">Dibayar</span>'; break;
                    case 'confirmed': statusBadge = '<span class="status-badge status-confirmed">Selesai</span>'; break;
                    case 'cancelled': statusBadge = '<span class="status-badge status-cancelled">Dibatalkan</span>'; break;
                }

                html += `
                    <tr>
                        <td>#${order.id}</td>
                        <td>${order.shop ? order.shop.name : '-'}</td>
                        <td>${order.buyer ? order.buyer.name : '-'}</td>
                        <td class="fw-bold">Rp ${Number(order.total_price).toLocaleString('id-ID')}</td>
                        <td>${statusBadge}</td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        } else {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">Belum ada pesanan pada platform.</td></tr>';
        }
    }

    async function updateShopStatus(id, status) {
        let actionWord = status === 'active' ? 'menyetujui' : 'memblokir';
        if (!confirm(`Apakah Anda yakin ingin ${actionWord} toko ini?`)) return;

        try {
            const res = await apiRequest(`/admin/shops/${id}`, 'PUT', { status });
            if (res.success) {
                alert(`Toko berhasil di-update menjadi ${status === 'active' ? 'Aktif' : 'Diblokir'}!`);
                await loadAdminData();
            } else {
                alert(res.message);
            }
        } catch (e) {
            alert('Gagal memperbarui status toko.');
        }
    }
</script>
@endsection
