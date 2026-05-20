@extends('layouts.app')

@section('title', $product->name . ' - UMKMart')

@section('content')
<section class="py-5">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb" style="font-size:0.9rem;">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none" style="color:var(--primary-light);">Beranda</a></li>
                @if($product->category)
                    <li class="breadcrumb-item"><a href="{{ route('home', ['category' => $product->category]) }}" class="text-decoration-none" style="color:var(--primary-light);">{{ $product->category }}</a></li>
                @endif
                <li class="breadcrumb-item text-secondary active">{{ $product->name }}</li>
            </ol>
        </nav>

        <div class="row g-5 fade-in">
            <!-- Product Image -->
            <div class="col-lg-5">
                <div class="glass-card p-0 overflow-hidden">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" class="w-100" style="height:400px;object-fit:cover;border-radius:16px;" alt="{{ $product->name }}">
                    @else
                        <div class="d-flex align-items-center justify-content-center" style="height:400px;background:linear-gradient(135deg,var(--dark-2),var(--dark-3));border-radius:16px;">
                            <i class="bi bi-box-seam" style="font-size:6rem;color:var(--text-muted);"></i>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Product Info -->
            <div class="col-lg-7">
                <div class="d-flex gap-2 mb-3">
                    <span class="badge-category">{{ $product->category ?? 'Umum' }}</span>
                    @if($product->stock > 0)
                        <span class="badge bg-success badge-stock" style="font-size:0.8rem;padding:0.4rem 0.8rem;">
                            <i class="bi bi-check-circle me-1"></i>Stok tersedia ({{ $product->stock }})
                        </span>
                    @else
                        <span class="badge bg-danger badge-stock" style="font-size:0.8rem;padding:0.4rem 0.8rem;">
                            <i class="bi bi-x-circle me-1"></i>Stok habis
                        </span>
                    @endif
                </div>

                <h1 class="fw-bold mb-3" style="font-size:1.75rem;">{{ $product->name }}</h1>

                <div class="price mb-4" style="font-size:2rem;">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </div>

                <div class="glass-card mb-4">
                    <h6 class="fw-bold mb-2"><i class="bi bi-text-paragraph me-2" style="color:var(--primary-light);"></i>Deskripsi</h6>
                    <p class="text-secondary mb-0">{{ $product->description ?? 'Tidak ada deskripsi.' }}</p>
                </div>

                <!-- Shop Info -->
                <div class="glass-card mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:50px;height:50px;border-radius:12px;background:linear-gradient(135deg,var(--primary),var(--secondary));display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-shop text-white fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">{{ $product->shop->name }}</h6>
                            <small class="text-muted">
                                <i class="bi bi-geo-alt me-1"></i>{{ $product->shop->address ?? 'Indonesia' }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Order Section -->
                <div class="glass-card" id="orderSection" style="display:none;">
                    <h6 class="fw-bold mb-3"><i class="bi bi-cart-plus me-2" style="color:var(--secondary);"></i>Pesan Produk</h6>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <label class="text-secondary">Jumlah:</label>
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-sm btn-outline-custom px-2 py-1" onclick="changeQty(-1)">
                                <i class="bi bi-dash"></i>
                            </button>
                            <input type="number" id="orderQty" value="1" min="1" max="{{ $product->stock }}" class="form-control form-control-dark text-center" style="width:70px;">
                            <button class="btn btn-sm btn-outline-custom px-2 py-1" onclick="changeQty(1)">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-secondary">Total:</span>
                        <span class="price" id="totalPrice" style="font-size:1.3rem;">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                    </div>
                    <button class="btn btn-primary-custom w-100" onclick="placeOrder()" id="btnOrder">
                        <i class="bi bi-bag-check me-2"></i>Buat Pesanan
                    </button>
                    <div id="orderResult" class="mt-3"></div>
                </div>

                <div id="loginPrompt" class="glass-card text-center" style="display:none;">
                    <p class="text-secondary mb-2">Silakan login sebagai buyer untuk memesan.</p>
                    <a href="{{ route('login') }}" class="btn btn-primary-custom">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Login
                    </a>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
            <div class="mt-5 pt-4" style="border-top:1px solid var(--border);">
                <h4 class="fw-bold mb-4">Produk Lainnya dari {{ $product->shop->name }}</h4>
                <div class="row g-4">
                    @foreach($relatedProducts as $related)
                        <div class="col-6 col-md-3">
                            <a href="{{ route('product.detail', $related->id) }}" class="text-decoration-none">
                                <div class="card-product">
                                    @if($related->image)
                                        <img src="{{ asset('storage/' . $related->image) }}" class="card-img-top" alt="{{ $related->name }}">
                                    @else
                                        <div class="product-img-placeholder">
                                            <i class="bi bi-box-seam"></i>
                                        </div>
                                    @endif
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $related->name }}</h6>
                                        <div class="price" style="font-size:1rem;">Rp {{ number_format($related->price, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
@endsection

@section('scripts')
<script>
    const productPrice = {{ $product->price }};
    const productId = {{ $product->id }};
    const shopId = {{ $product->shop_id }};
    const maxStock = {{ $product->stock }};

    // Show order section based on auth state
    document.addEventListener('DOMContentLoaded', function() {
        const user = getUser();
        const token = getToken();
        if (token && user && user.role === 'buyer') {
            document.getElementById('orderSection').style.display = 'block';
        } else if (!token) {
            document.getElementById('loginPrompt').style.display = 'block';
        }
    });

    function changeQty(delta) {
        const input = document.getElementById('orderQty');
        let val = parseInt(input.value) + delta;
        if (val < 1) val = 1;
        if (val > maxStock) val = maxStock;
        input.value = val;
        updateTotal();
    }

    document.getElementById('orderQty')?.addEventListener('change', updateTotal);

    function updateTotal() {
        const qty = parseInt(document.getElementById('orderQty').value) || 1;
        const total = productPrice * qty;
        document.getElementById('totalPrice').textContent = formatRupiah(total);
    }

    async function placeOrder() {
        const qty = parseInt(document.getElementById('orderQty').value) || 1;
        const btn = document.getElementById('btnOrder');
        const resultDiv = document.getElementById('orderResult');

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';

        try {
            const res = await apiRequest('/buyer/orders', 'POST', {
                shop_id: shopId,
                items: [{ product_id: productId, quantity: qty }],
                notes: ''
            });

            if (res.success) {
                resultDiv.innerHTML = `
                    <div class="alert alert-custom border-success">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        <strong>Pesanan berhasil dibuat!</strong> Order #${res.data.id}
                        <br><small class="text-muted">Total: ${formatRupiah(res.data.total_price)}</small>
                    </div>`;
                btn.innerHTML = '<i class="bi bi-check me-2"></i>Pesanan Dibuat';
            } else {
                resultDiv.innerHTML = `<div class="alert alert-custom border-danger"><i class="bi bi-exclamation-triangle text-danger me-2"></i>${res.message}</div>`;
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-bag-check me-2"></i>Buat Pesanan';
            }
        } catch (e) {
            resultDiv.innerHTML = `<div class="alert alert-custom border-danger"><i class="bi bi-exclamation-triangle text-danger me-2"></i>Terjadi kesalahan.</div>`;
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-bag-check me-2"></i>Buat Pesanan';
        }
    }
</script>
@endsection
