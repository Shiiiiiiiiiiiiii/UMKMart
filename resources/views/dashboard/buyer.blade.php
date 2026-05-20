@extends('layouts.app')

@section('title', 'Dashboard Buyer - UMKMart')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-1" id="welcomeText">Dashboard Buyer</h2>
            <p class="text-secondary">Pantau riwayat pesanan Anda</p>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="stat-card fade-in">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-bag"></i>
                    </div>
                    <div>
                        <div class="stat-label">Total Pesanan</div>
                        <div class="stat-value" id="totalOrders">0</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card fade-in" style="animation-delay: 0.1s;">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <div class="stat-label">Menunggu Pembayaran</div>
                        <div class="stat-value" id="waitingPayment">0</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card fade-in" style="animation-delay: 0.2s;">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div>
                        <div class="stat-label">Selesai</div>
                        <div class="stat-value" id="completedOrders">0</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="glass-card fade-in" style="animation-delay: 0.3s;">
        <h5 class="fw-bold mb-4"><i class="bi bi-list-ul me-2 text-primary"></i>Riwayat Pesanan</h5>
        
        <div class="table-responsive">
            <table class="table table-dark-custom mb-0">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Toko</th>
                        <th>Item</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="ordersTableBody">
                    <tr><td colspan="6" class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-2"></span>Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Upload Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border border-secondary rounded-4">
            <div class="modal-header border-bottom border-secondary">
                <h5 class="modal-title">Upload Bukti Pembayaran</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="paymentForm">
                    <input type="hidden" id="uploadOrderId">
                    <div class="mb-3">
                        <label class="form-label form-label-custom">Pilih File (JPG/PNG)</label>
                        <input type="file" class="form-control form-control-dark" id="paymentProof" accept="image/*" required>
                    </div>
                    <button type="submit" class="btn btn-primary-custom w-100" id="btnUploadPayment">
                        <i class="bi bi-cloud-upload me-2"></i>Upload
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const user = getUser();
        if (!user || user.role !== 'buyer') {
            window.location.href = '/login';
            return;
        }

        document.getElementById('welcomeText').textContent = `Halo, ${user.name}`;
        await loadOrders();
    });

    async function loadOrders() {
        try {
            const res = await apiRequest('/buyer/orders');
            const tbody = document.getElementById('ordersTableBody');
            
            if (res.success && res.data.length > 0) {
                const orders = res.data;
                let html = '';
                
                let waiting = 0, completed = 0;

                orders.forEach(order => {
                    if (order.status === 'pending' || order.status === 'waiting_payment') waiting++;
                    if (order.status === 'confirmed') completed++;

                    let statusBadge = '';
                    switch(order.status) {
                        case 'pending': statusBadge = '<span class="status-badge status-pending">Pending</span>'; break;
                        case 'waiting_payment': statusBadge = '<span class="status-badge status-waiting_payment">Menunggu Pembayaran</span>'; break;
                        case 'paid': statusBadge = '<span class="status-badge status-active">Dibayar</span>'; break;
                        case 'confirmed': statusBadge = '<span class="status-badge status-confirmed">Selesai</span>'; break;
                        case 'cancelled': statusBadge = '<span class="status-badge status-cancelled">Dibatalkan</span>'; break;
                    }

                    const items = order.order_items.map(i => `${i.product.name} (x${i.quantity})`).join('<br>');
                    
                    let actionBtn = '';
                    if (order.status === 'pending' || order.status === 'waiting_payment') {
                        actionBtn = `
                            <button class="btn btn-sm btn-success me-1" onclick="openPaymentModal(${order.id})" title="Upload Bukti">
                                <i class="bi bi-wallet2"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="cancelOrder(${order.id})" title="Batalkan">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        `;
                    }

                    html += `
                        <tr>
                            <td class="fw-bold">#${order.id}</td>
                            <td><i class="bi bi-shop me-1 text-secondary"></i>${order.shop.name}</td>
                            <td><small>${items}</small></td>
                            <td class="fw-bold text-primary-light">${formatRupiah(order.total_price)}</td>
                            <td>${statusBadge}</td>
                            <td>${actionBtn || '-'}</td>
                        </tr>
                    `;
                });
                
                tbody.innerHTML = html;
                document.getElementById('totalOrders').textContent = orders.length;
                document.getElementById('waitingPayment').textContent = waiting;
                document.getElementById('completedOrders').textContent = completed;
            } else {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">Belum ada pesanan.</td></tr>';
            }
        } catch (error) {
            console.error('Error fetching orders:', error);
        }
    }

    function openPaymentModal(orderId) {
        document.getElementById('uploadOrderId').value = orderId;
        const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
        modal.show();
    }

    document.getElementById('paymentForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const orderId = document.getElementById('uploadOrderId').value;
        const fileInput = document.getElementById('paymentProof');
        
        if (!fileInput.files[0]) return;

        const formData = new FormData();
        formData.append('payment_proof', fileInput.files[0]);
        // Laravel requires spoofing PUT/PATCH with form data
        formData.append('_method', 'PUT'); 

        const btn = document.getElementById('btnUploadPayment');
        btn.disabled = true;
        btn.innerHTML = 'Uploading...';

        try {
            const token = getToken();
            const res = await fetch(`/api/buyer/orders/${orderId}`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                },
                body: formData
            });
            const data = await res.json();

            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
                await loadOrders();
            } else {
                alert(data.message || 'Gagal upload');
            }
        } catch (error) {
            alert('Terjadi kesalahan jaringan.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-cloud-upload me-2"></i>Upload';
        }
    });

    async function cancelOrder(orderId) {
        if (!confirm('Yakin ingin membatalkan pesanan ini?')) return;
        
        try {
            const res = await apiRequest(`/buyer/orders/${orderId}`, 'PUT', { status: 'cancelled' });
            if (res.success) {
                await loadOrders();
            } else {
                alert(res.message);
            }
        } catch (e) {
            alert('Kesalahan jaringan');
        }
    }
</script>
@endsection
