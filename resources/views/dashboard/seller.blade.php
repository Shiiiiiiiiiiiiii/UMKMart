@extends('layouts.app')

@section('title', 'Dashboard Seller - UMKMart')

@section('content')
<div class="container py-5" id="app-container" style="display:none;">
    <!-- If No Shop -->
    <div id="noShopSection" class="text-center py-5 glass-card fade-in" style="display:none;max-width:600px;margin:0 auto;">
        <i class="bi bi-shop-window text-secondary mb-3" style="font-size:4rem;"></i>
        <h3 class="fw-bold">Buka Toko Anda</h3>
        <p class="text-muted mb-4">Anda belum memiliki toko. Silakan buat toko terlebih dahulu untuk mulai berjualan.</p>
        
        <form id="createShopForm" class="text-start">
            <div class="mb-3">
                <label class="form-label form-label-custom">Nama Toko</label>
                <input type="text" class="form-control form-control-dark" id="shopName" required>
            </div>
            <div class="mb-3">
                <label class="form-label form-label-custom">Deskripsi</label>
                <textarea class="form-control form-control-dark" id="shopDesc" rows="2"></textarea>
            </div>
            <div class="mb-4">
                <label class="form-label form-label-custom">Alamat Lengkap</label>
                <textarea class="form-control form-control-dark" id="shopAddress" rows="2"></textarea>
            </div>
            <button type="submit" class="btn btn-primary-custom w-100" id="btnCreateShop">
                Buat Toko
            </button>
        </form>
    </div>

    <!-- Main Seller Dashboard -->
    <div id="sellerDashboard" style="display:none;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1"><i class="bi bi-shop me-2 text-secondary"></i><span id="displayShopName">Toko Saya</span></h2>
                <span class="badge bg-success" id="shopStatus">Status</span>
            </div>
            <div>
                <button class="btn btn-outline-custom btn-sm" onclick="showApiKeyModal()">
                    <i class="bi bi-key me-1"></i>API Key
                </button>
            </div>
        </div>

        <ul class="nav nav-pills mb-4" id="sellerTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-orders" type="button">Pesanan Masuk</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-products" type="button">Produk Saya</button>
            </li>
        </ul>

        <div class="tab-content" id="sellerTabsContent">
            <!-- Orders Tab -->
            <div class="tab-pane fade show active" id="tab-orders" role="tabpanel">
                <div class="glass-card">
                    <h5 class="fw-bold mb-3">Kelola Pesanan</h5>
                    <div class="table-responsive">
                        <table class="table table-dark-custom mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Pembeli</th>
                                    <th>Item</th>
                                    <th>Total</th>
                                    <th>Bukti Bayar</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="sellerOrdersBody">
                                <tr><td colspan="7" class="text-center py-4">Memuat data...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Products Tab -->
            <div class="tab-pane fade" id="tab-products" role="tabpanel">
                <div class="glass-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Daftar Produk</h5>
                        <button class="btn btn-sm btn-primary-custom" data-bs-toggle="modal" data-bs-target="#addProductModal">
                            <i class="bi bi-plus-lg me-1"></i>Tambah Produk
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-dark-custom mb-0">
                            <thead>
                                <tr>
                                    <th>Info Produk</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="sellerProductsBody">
                                <tr><td colspan="5" class="text-center py-4">Memuat data...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Product Modal (Requires API Key Header) -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark border border-secondary rounded-4">
            <div class="modal-header border-bottom border-secondary">
                <h5 class="modal-title">Tambah Produk Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info py-2 small border-0 bg-primary bg-opacity-10 text-primary-light">
                    <i class="bi bi-info-circle me-1"></i>Pembuatan produk memerlukan otentikasi API Key Toko.
                </div>
                <form id="productForm">
                    <div class="mb-3">
                        <label class="form-label form-label-custom">Nama Produk</label>
                        <input type="text" class="form-control form-control-dark" id="prodName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label form-label-custom">Kategori</label>
                        <input type="text" class="form-control form-control-dark" id="prodCat">
                    </div>
                    <div class="mb-3">
                        <label class="form-label form-label-custom">Deskripsi Produk</label>
                        <textarea class="form-control form-control-dark" id="prodDesc" rows="3" placeholder="Masukkan deskripsi detail mengenai produk..."></textarea>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label form-label-custom">Harga (Rp)</label>
                            <input type="number" class="form-control form-control-dark" id="prodPrice" required min="0">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label form-label-custom">Stok</label>
                            <input type="number" class="form-control form-control-dark" id="prodStock" required min="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label form-label-custom">Gambar</label>
                        <input type="file" class="form-control form-control-dark" id="prodImg" accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-primary-custom w-100" id="btnSaveProduct">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark border border-secondary rounded-4">
            <div class="modal-header border-bottom border-secondary">
                <h5 class="modal-title">Edit Produk</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editProductForm">
                    <input type="hidden" id="editProdId">
                    <div class="mb-3">
                        <label class="form-label form-label-custom">Nama Produk</label>
                        <input type="text" class="form-control form-control-dark" id="editProdName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label form-label-custom">Kategori</label>
                        <input type="text" class="form-control form-control-dark" id="editProdCat">
                    </div>
                    <div class="mb-3">
                        <label class="form-label form-label-custom">Deskripsi Produk</label>
                        <textarea class="form-control form-control-dark" id="editProdDesc" rows="3" placeholder="Masukkan deskripsi detail mengenai produk..."></textarea>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label form-label-custom">Harga (Rp)</label>
                            <input type="number" class="form-control form-control-dark" id="editProdPrice" required min="0">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label form-label-custom">Stok</label>
                            <input type="number" class="form-control form-control-dark" id="editProdStock" required min="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label form-label-custom">Gambar Baru (Opsional)</label>
                        <input type="file" class="form-control form-control-dark" id="editProdImg" accept="image/*">
                        <small class="text-muted d-block mt-1">Biarkan kosong jika tidak ingin mengubah gambar.</small>
                    </div>
                    <button type="submit" class="btn btn-primary-custom w-100" id="btnUpdateProduct">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- API Key Modal -->
<div class="modal fade" id="apiKeyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark border border-secondary rounded-4">
            <div class="modal-header border-bottom border-secondary">
                <h5 class="modal-title">API Key Toko</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <p class="small text-secondary mb-3">Gunakan API Key ini pada Header <code>X-API-KEY</code> untuk manajemen produk via REST API eksternal.</p>
                <div class="bg-dark-2 p-3 rounded border border-secondary mb-3 text-break user-select-all font-monospace" id="displayApiKey" style="color:var(--primary-light);">
                    -
                </div>
                <button class="btn btn-outline-danger btn-sm" onclick="regenerateKey()">
                    <i class="bi bi-arrow-repeat me-1"></i>Regenerate Key
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bukti Bayar Modal -->
<div class="modal fade" id="proofModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border border-secondary rounded-4">
            <div class="modal-body p-4 text-center">
                <img src="" id="proofImage" class="img-fluid rounded mb-3" style="max-height:60vh;">
                <button type="button" class="btn btn-outline-custom w-100" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
    .nav-pills .nav-link { color: var(--text-secondary); padding: 0.5rem 1.5rem; border-radius: 20px; }
    .nav-pills .nav-link.active { background: var(--primary); color: white; }
</style>
<script>
    let currentShop = null;
    let shopApiKey = '';

    document.addEventListener('DOMContentLoaded', async () => {
        const user = getUser();
        if (!user || user.role !== 'seller') {
            window.location.href = '/login';
            return;
        }

        document.getElementById('app-container').style.display = 'block';
        await checkShop();
    });

    async function checkShop() {
        try {
            const res = await apiRequest('/seller/my-shop');
            if (res.success) {
                currentShop = res.data;
                shopApiKey = currentShop.api_key;
                showDashboard();
            } else {
                document.getElementById('noShopSection').style.display = 'block';
                document.getElementById('sellerDashboard').style.display = 'none';
            }
        } catch (e) {
            console.error('Error fetching shop');
        }
    }

    // Create Shop
    document.getElementById('createShopForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('btnCreateShop');
        btn.disabled = true;

        const data = {
            name: document.getElementById('shopName').value,
            description: document.getElementById('shopDesc').value,
            address: document.getElementById('shopAddress').value
        };

        try {
            const res = await apiRequest('/seller/shops', 'POST', data);
            if (res.success) {
                alert('Toko berhasil dibuat! Menunggu aktivasi admin.');
                await checkShop();
            } else {
                alert(res.message);
            }
        } catch(e) {
            alert('Terjadi kesalahan');
        } finally {
            btn.disabled = false;
        }
    });

    function showDashboard() {
        document.getElementById('noShopSection').style.display = 'none';
        document.getElementById('sellerDashboard').style.display = 'block';
        
        document.getElementById('displayShopName').textContent = currentShop.name;
        document.getElementById('displayApiKey').textContent = shopApiKey;
        
        const statusBadge = document.getElementById('shopStatus');
        statusBadge.textContent = currentShop.status;
        if(currentShop.status === 'active') statusBadge.className = 'badge bg-success';
        else if(currentShop.status === 'pending') statusBadge.className = 'badge bg-warning text-dark';
        else statusBadge.className = 'badge bg-danger';

        loadOrders();
        loadProducts();
    }

    // Orders
    async function loadOrders() {
        const res = await apiRequest('/seller/orders');
        const tbody = document.getElementById('sellerOrdersBody');
        
        if (res.success && res.data.length > 0) {
            let html = '';
            res.data.forEach(order => {
                let statusBadge = '';
                switch(order.status) {
                    case 'pending': statusBadge = '<span class="status-badge status-pending">Pending</span>'; break;
                    case 'waiting_payment': statusBadge = '<span class="status-badge status-waiting_payment">Menunggu Pembayaran</span>'; break;
                    case 'paid': statusBadge = '<span class="status-badge status-active">Dibayar (Cek Bukti)</span>'; break;
                    case 'confirmed': statusBadge = '<span class="status-badge status-confirmed">Selesai</span>'; break;
                    case 'cancelled': statusBadge = '<span class="status-badge status-cancelled">Dibatalkan</span>'; break;
                }

                let actions = '';
                if(order.status === 'pending') {
                    actions = `<button class="btn btn-sm btn-outline-warning" onclick="updateOrderStatus(${order.id}, 'waiting_payment')">Tagih</button>`;
                } else if(order.status === 'paid') {
                    actions = `<button class="btn btn-sm btn-success" onclick="updateOrderStatus(${order.id}, 'confirmed')">Konfirmasi</button>`;
                }
                if(order.status !== 'cancelled' && order.status !== 'confirmed') {
                    actions += ` <button class="btn btn-sm btn-outline-danger" onclick="updateOrderStatus(${order.id}, 'cancelled')">Batal</button>`;
                }

                const proofBtn = order.payment_proof 
                    ? `<button class="btn btn-sm btn-info text-white" onclick="viewProof('${order.payment_proof}')"><i class="bi bi-image"></i></button>` 
                    : '-';

                html += `
                    <tr>
                        <td>#${order.id}</td>
                        <td>${order.buyer.name}</td>
                        <td><small>${order.order_items.map(i => `${i.product.name} (x${i.quantity})`).join(', ')}</small></td>
                        <td class="fw-bold">Rp ${Number(order.total_price).toLocaleString('id-ID')}</td>
                        <td>${proofBtn}</td>
                        <td>${statusBadge}</td>
                        <td>${actions || '-'}</td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        } else {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4">Belum ada pesanan</td></tr>';
        }
    }

    async function updateOrderStatus(id, status) {
        if(!confirm(`Ubah status menjadi ${status}?`)) return;
        const res = await apiRequest(`/seller/orders/${id}`, 'PUT', { status });
        if(res.success) loadOrders();
        else alert(res.message);
    }

    function viewProof(path) {
        document.getElementById('proofImage').src = `/storage/${path}`;
        new bootstrap.Modal(document.getElementById('proofModal')).show();
    }

    // Products
    function loadProducts() {
        const tbody = document.getElementById('sellerProductsBody');
        const products = currentShop.products;
        
        if (products && products.length > 0) {
            let html = '';
            products.forEach(prod => {
                const img = prod.image ? `<img src="/storage/${prod.image}" width="40" height="40" class="rounded me-2 object-fit-cover">` : '<div class="bg-dark rounded me-2 d-inline-block text-center" style="width:40px;height:40px;line-height:40px;"><i class="bi bi-box"></i></div>';
                html += `
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                ${img}
                                <span>${prod.name}</span>
                            </div>
                        </td>
                        <td><span class="badge-category">${prod.category || '-'}</span></td>
                        <td>Rp ${Number(prod.price).toLocaleString('id-ID')}</td>
                        <td>${prod.stock}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-warning me-1" onclick="editProduct(${prod.id})"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteProduct(${prod.id})"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        } else {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4">Belum ada produk</td></tr>';
        }
    }

    // Add Product using API Key Header
    document.getElementById('productForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        if (currentShop.status !== 'active') {
            alert('Toko Anda harus aktif sebelum bisa menambahkan produk.');
            return;
        }

        const formData = new FormData();
        formData.append('name', document.getElementById('prodName').value);
        formData.append('category', document.getElementById('prodCat').value);
        formData.append('description', document.getElementById('prodDesc').value);
        formData.append('price', document.getElementById('prodPrice').value);
        formData.append('stock', document.getElementById('prodStock').value);
        
        const fileInput = document.getElementById('prodImg');
        if (fileInput.files[0]) {
            formData.append('image', fileInput.files[0]);
        }

        const btn = document.getElementById('btnSaveProduct');
        btn.disabled = true;

        try {
            const token = getToken();
            const res = await fetch(`/api/seller/products`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'X-API-KEY': shopApiKey, // CRITICAL: API Key Middleware check
                    'Accept': 'application/json'
                },
                body: formData
            });
            const data = await res.json();

            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('addProductModal')).hide();
                document.getElementById('productForm').reset();
                await checkShop(); // reload data
            } else {
                alert(data.message || 'Gagal menyimpan');
            }
        } catch (e) {
            alert('Kesalahan jaringan');
        } finally {
            btn.disabled = false;
        }
    });

    async function deleteProduct(id) {
        if(!confirm('Hapus produk ini?')) return;
        const res = await fetch(`/api/seller/products/${id}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${getToken()}`,
                'X-API-KEY': shopApiKey,
                'Accept': 'application/json'
            }
        });
        const data = await res.json();
        if(data.success) await checkShop();
        else alert(data.message);
    }

    function showApiKeyModal() {
        new bootstrap.Modal(document.getElementById('apiKeyModal')).show();
    }

    async function regenerateKey() {
        if(!confirm('Regenerate API Key? Aplikasi yang menggunakan key lama tidak akan bisa mengakses API.')) return;
        const res = await apiRequest('/seller/my-shop/regenerate-key', 'POST');
        if(res.success) {
            shopApiKey = res.data.api_key;
            document.getElementById('displayApiKey').textContent = shopApiKey;
            alert('API Key berhasil di-regenerate!');
        }
    }

    // Edit Product JS implementation
    function editProduct(id) {
        const prod = currentShop.products.find(p => p.id === id);
        if (!prod) return;

        document.getElementById('editProdId').value = prod.id;
        document.getElementById('editProdName').value = prod.name;
        document.getElementById('editProdCat').value = prod.category || '';
        document.getElementById('editProdDesc').value = prod.description || '';
        document.getElementById('editProdPrice').value = prod.price;
        document.getElementById('editProdStock').value = prod.stock;
        
        // Reset file upload
        document.getElementById('editProdImg').value = '';

        new bootstrap.Modal(document.getElementById('editProductModal')).show();
    }

    document.getElementById('editProductForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const id = document.getElementById('editProdId').value;
        const btn = document.getElementById('btnUpdateProduct');
        btn.disabled = true;

        const formData = new FormData();
        // Standard Laravel spoofing for PUT requests using POST
        formData.append('_method', 'PUT');
        formData.append('name', document.getElementById('editProdName').value);
        formData.append('category', document.getElementById('editProdCat').value);
        formData.append('description', document.getElementById('editProdDesc').value);
        formData.append('price', document.getElementById('editProdPrice').value);
        formData.append('stock', document.getElementById('editProdStock').value);
        
        const fileInput = document.getElementById('editProdImg');
        if (fileInput.files[0]) {
            formData.append('image', fileInput.files[0]);
        }

        try {
            const token = getToken();
            const res = await fetch(`/api/seller/products/${id}`, {
                method: 'POST', // Spoofed to PUT
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'X-API-KEY': shopApiKey,
                    'Accept': 'application/json'
                },
                body: formData
            });
            const data = await res.json();

            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('editProductModal')).hide();
                alert('Produk berhasil diperbarui!');
                await checkShop(); // reload data
            } else {
                alert(data.message || 'Gagal menyimpan perubahan');
            }
        } catch (e) {
            alert('Kesalahan jaringan');
        } finally {
            btn.disabled = false;
        }
    });
</script>
@endsection
