@extends('layouts.app')

@section('title', 'UMKMart - Marketplace UMKM Lokal Terpercaya')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container position-relative" style="z-index:1;">
        <div class="row align-items-center">
            <div class="col-lg-7 fade-in">
                <h1 class="hero-title mb-3">
                    Temukan Produk <span>UMKM Terbaik</span><br>
                </h1>
                <p class="text-secondary mb-4" style="font-size:1.1rem;max-width:500px;">
                    Dukung pelaku usaha lokal dengan berbelanja produk berkualitas langsung dari UMKM terpercaya.
                </p>
                <form action="{{ route('home') }}" method="GET" class="d-flex gap-2" style="max-width:480px;">
                    <input type="text" name="search" class="form-control search-box" placeholder="Cari produk..." value="{{ request('search') }}" id="searchInput">
                    <button type="submit" class="btn btn-primary-custom px-4">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
            </div>
            <div class="col-lg-5 d-none d-lg-flex justify-content-center fade-in">
                <div class="position-relative">
                    <div style="width:280px;height:280px;background:linear-gradient(135deg,var(--primary),var(--secondary));border-radius:50%;opacity:0.15;position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);"></div>
                    <i class="bi bi-shop-window" style="font-size:10rem;color:var(--primary-light);position:relative;z-index:1;opacity:0.7;"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Category Filter -->
<section class="py-4" style="background:var(--dark-2);border-bottom:1px solid var(--border);">
    <div class="container">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="text-secondary small me-2"><i class="bi bi-funnel me-1"></i>Filter:</span>
            <a href="{{ route('home') }}" class="filter-pill {{ !request('category') ? 'active' : '' }}">Semua</a>
            @foreach($categories as $cat)
                <a href="{{ route('home', ['category' => $cat, 'search' => request('search')]) }}"
                   class="filter-pill {{ request('category') == $cat ? 'active' : '' }}">
                    {{ $cat }}
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Products Grid -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h4 fw-bold mb-1">
                    @if(request('search'))
                        Hasil pencarian: "{{ request('search') }}"
                    @elseif(request('category'))
                        Kategori: {{ request('category') }}
                    @else
                        Produk Terbaru
                    @endif
                </h2>
                <p class="text-secondary small mb-0">{{ $products->total() }} produk ditemukan</p>
            </div>
        </div>

        @if($products->count() > 0)
            <div class="row g-4">
                @foreach($products as $product)
                    <div class="col-6 col-md-4 col-lg-3 fade-in">
                        <a href="{{ route('product.detail', $product->id) }}" class="text-decoration-none">
                            <div class="card-product">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                                @else
                                    <div class="product-img-placeholder">
                                        <i class="bi bi-box-seam"></i>
                                    </div>
                                @endif
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="badge-category">{{ $product->category ?? 'Umum' }}</span>
                                        @if($product->stock > 0)
                                            <span class="badge bg-success badge-stock">Stok: {{ $product->stock }}</span>
                                        @else
                                            <span class="badge bg-danger badge-stock">Habis</span>
                                        @endif
                                    </div>
                                    <h6 class="card-title">{{ $product->name }}</h6>
                                    <p class="shop-name mb-2">
                                        <i class="bi bi-shop me-1"></i>{{ $product->shop->name ?? '-' }}
                                    </p>
                                    <div class="price">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-5">
                {{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-inbox" style="font-size:4rem;color:var(--text-muted);"></i>
                <h5 class="mt-3 text-secondary">Produk tidak ditemukan</h5>
                <p class="text-muted">Coba kata kunci lain atau lihat semua produk.</p>
                <a href="{{ route('home') }}" class="btn btn-outline-custom mt-2">Lihat Semua Produk</a>
            </div>
        @endif
    </div>
</section>
@endsection
