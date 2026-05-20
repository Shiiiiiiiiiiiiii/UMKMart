<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="UMKMart - Marketplace UMKM lokal terpercaya. Temukan produk UMKM berkualitas dari seluruh Indonesia.">
    <title>@yield('title', 'UMKMart - Marketplace UMKM Lokal')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #6C5CE7;
            --primary-dark: #5A4BD1;
            --primary-light: #A29BFE;
            --secondary: #00CEC9;
            --accent: #FD79A8;
            --dark: #0D1117;
            --dark-2: #161B22;
            --dark-3: #21262D;
            --text-primary: #F8F9FA;
            --text-secondary: #B2BEC3;
            --text-muted: #85929E;
            --border: #30363D;
            --success: #2EA043;
            --warning: #D29922;
            --danger: #F85149;
            --card-bg: rgba(22, 27, 34, 0.8);
            --glass: rgba(255, 255, 255, 0.05);
        }

        /* Override Bootstrap utility classes for text readability on dark background */
        .text-muted {
            color: var(--text-muted) !important;
        }

        .text-secondary {
            color: var(--text-secondary) !important;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--dark);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── Navbar ────────────────────────────── */
        .navbar-custom {
            background: rgba(13, 17, 23, 0.85);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            padding: 0.75rem 0;
            transition: all 0.3s ease;
        }

        .navbar-custom .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
        }

        .navbar-custom .nav-link {
            color: var(--text-secondary);
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .navbar-custom .nav-link:hover,
        .navbar-custom .nav-link.active {
            color: var(--text-primary);
            background: var(--glass);
        }

        .btn-nav {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white !important;
            border: none;
            padding: 0.5rem 1.25rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-nav:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(108, 92, 231, 0.4);
        }

        /* ── Cards ─────────────────────────────── */
        .card-product {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
        }

        .card-product:hover {
            transform: translateY(-8px);
            border-color: var(--primary);
            box-shadow: 0 20px 40px rgba(108, 92, 231, 0.15);
        }

        .card-product .card-img-top {
            height: 200px;
            object-fit: cover;
            border-bottom: 1px solid var(--border);
        }

        .card-product .card-body {
            padding: 1.25rem;
        }

        .card-product .card-title {
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }

        .card-product .shop-name {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .card-product .price {
            font-size: 1.2rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--secondary), #55EFC4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .badge-category {
            background: var(--glass);
            border: 1px solid var(--border);
            color: var(--primary-light);
            font-weight: 500;
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
        }

        .badge-stock {
            font-size: 0.75rem;
            padding: 0.25rem 0.6rem;
            border-radius: 20px;
        }

        /* ── Buttons ───────────────────────────── */
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(108, 92, 231, 0.4);
            color: white;
        }

        .btn-outline-custom {
            border: 1.5px solid var(--primary);
            color: var(--primary-light);
            padding: 0.6rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            background: transparent;
            transition: all 0.3s ease;
        }

        .btn-outline-custom:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
        }

        /* ── Hero ──────────────────────────────── */
        .hero-section {
            background: linear-gradient(135deg, rgba(108,92,231,0.15) 0%, rgba(0,206,201,0.1) 100%);
            border-bottom: 1px solid var(--border);
            padding: 4rem 0;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(108,92,231,0.1) 0%, transparent 70%);
            border-radius: 50%;
        }

        .hero-section::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(0,206,201,0.08) 0%, transparent 70%);
            border-radius: 50%;
        }

        .hero-title {
            font-size: 2.5rem;
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: -1px;
        }

        .hero-title span {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ── Search ────────────────────────────── */
        .search-box {
            background: var(--dark-2);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 0.6rem 1rem;
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        .search-box:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.15);
            background: var(--dark-3);
            color: var(--text-primary);
            outline: none;
        }

        /* ── Filter Pills ──────────────────────── */
        .filter-pill {
            background: var(--dark-2);
            border: 1px solid var(--border);
            color: var(--text-secondary);
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .filter-pill:hover,
        .filter-pill.active {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }

        /* ── Footer ────────────────────────────── */
        .footer-custom {
            background: var(--dark-2);
            border-top: 1px solid var(--border);
            padding: 2rem 0;
            color: var(--text-muted);
        }

        /* ── Glass Card ────────────────────────── */
        .glass-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.5rem;
        }

        /* ── Auth Forms ────────────────────────── */
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--dark);
            position: relative;
        }

        .auth-container::before {
            content: '';
            position: absolute;
            top: 20%;
            left: 30%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(108,92,231,0.08) 0%, transparent 70%);
            border-radius: 50%;
        }

        .auth-card {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 2.5rem;
            width: 100%;
            max-width: 450px;
            position: relative;
            z-index: 1;
        }

        .form-control-dark {
            background: var(--dark-2);
            border: 1px solid var(--border);
            color: var(--text-primary);
            border-radius: 10px;
            padding: 0.65rem 1rem;
        }

        .form-control-dark:focus {
            background: var(--dark-3);
            border-color: var(--primary);
            color: var(--text-primary);
            box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.15);
        }

        .form-control-dark::placeholder {
            color: var(--text-muted);
        }

        .form-label-custom {
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 0.875rem;
            margin-bottom: 0.4rem;
        }

        .form-select-dark {
            background: var(--dark-2);
            border: 1px solid var(--border);
            color: var(--text-primary);
            border-radius: 10px;
            padding: 0.65rem 1rem;
        }

        .form-select-dark:focus {
            background: var(--dark-3);
            border-color: var(--primary);
            color: var(--text-primary);
            box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.15);
        }

        /* ── Dashboard ─────────────────────────── */
        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            border-color: var(--primary);
            transform: translateY(-4px);
        }

        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }

        .stat-card .stat-value {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--text-primary);
        }

        .stat-card .stat-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .table-dark-custom {
            --bs-table-bg: transparent;
            --bs-table-border-color: var(--border);
            color: var(--text-primary);
        }

        .table-dark-custom th {
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border);
            padding: 1rem 0.75rem;
        }

        .table-dark-custom td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
            border-bottom: 1px solid rgba(48, 54, 61, 0.5);
        }

        .status-badge {
            padding: 0.3rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-pending { background: rgba(210, 153, 34, 0.15); color: var(--warning); }
        .status-active, .status-paid, .status-confirmed { background: rgba(46, 160, 67, 0.15); color: var(--success); }
        .status-cancelled, .status-banned { background: rgba(248, 81, 73, 0.15); color: var(--danger); }
        .status-waiting_payment { background: rgba(108, 92, 231, 0.15); color: var(--primary-light); }

        /* ── Animations ────────────────────────── */
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ── Scrollbar ─────────────────────────── */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--dark);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--dark-3);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--text-muted);
        }

        /* ── Alert ─────────────────────────────── */
        .alert-custom {
            background: var(--dark-2);
            border: 1px solid var(--border);
            border-radius: 12px;
            color: var(--text-primary);
            padding: 1rem 1.25rem;
        }

        .product-img-placeholder {
            height: 200px;
            background: linear-gradient(135deg, var(--dark-2), var(--dark-3));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            font-size: 3rem;
        }

        /* ── Responsive ────────────────────────── */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 1.75rem;
            }
            .hero-section {
                padding: 2.5rem 0;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top" id="mainNavbar">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="bi bi-shop-window me-2"></i>UMKMart
            </a>
            <button class="navbar-toggler border-0 text-light" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
                <i class="bi bi-list fs-4"></i>
            </button>
            <div class="collapse navbar-collapse" id="navContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="bi bi-house me-1"></i>Beranda
                        </a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-2" id="navAuth">
                    <!-- Auth buttons will be dynamically rendered by JS -->
                    <a href="{{ route('login') }}" class="nav-link" id="btnLoginNav">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Masuk
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-nav" id="btnRegisterNav">Daftar</a>
                    <!-- Logged-in state (hidden by default) -->
                    <div class="dropdown d-none" id="userDropdown">
                        <button class="btn btn-outline-custom dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i><span id="userName">User</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" style="background:var(--dark-2);border-color:var(--border);">
                            <li><a class="dropdown-item text-light" href="#" id="btnDashboard"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                            <li><hr class="dropdown-divider" style="border-color:var(--border);"></li>
                            <li><a class="dropdown-item text-danger" href="#" id="btnLogout"><i class="bi bi-box-arrow-right me-2"></i>Keluar</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer-custom">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-1" style="font-weight:700;">
                        <span style="background:linear-gradient(135deg,var(--primary),var(--secondary));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">
                            UMKMart
                        </span>
                    </h5>
                    <p class="mb-0 small">Marketplace UMKM lokal terpercaya di Indonesia.</p>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    <small>&copy; {{ date('Y') }} UMKMart. All rights reserved.</small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // ── Auth Helper ──────────────────────────
        const API_URL = '/api';

        function getToken() {
            return localStorage.getItem('umkmart_token');
        }

        function getUser() {
            const u = localStorage.getItem('umkmart_user');
            return u ? JSON.parse(u) : null;
        }

        function setAuth(token, user) {
            localStorage.setItem('umkmart_token', token);
            localStorage.setItem('umkmart_user', JSON.stringify(user));
        }

        function clearAuth() {
            localStorage.removeItem('umkmart_token');
            localStorage.removeItem('umkmart_user');
        }

        async function apiRequest(endpoint, method = 'GET', body = null, isFormData = false) {
            const headers = {};
            const token = getToken();
            if (token) headers['Authorization'] = `Bearer ${token}`;
            if (!isFormData) headers['Content-Type'] = 'application/json';
            headers['Accept'] = 'application/json';

            const options = { method, headers };
            if (body) {
                options.body = isFormData ? body : JSON.stringify(body);
            }

            const res = await fetch(`${API_URL}${endpoint}`, options);
            return await res.json();
        }

        function formatRupiah(num) {
            return 'Rp ' + Number(num).toLocaleString('id-ID');
        }

        // ── Nav Auth State ───────────────────────
        function updateNavAuth() {
            const user = getUser();
            const token = getToken();

            if (token && user) {
                document.getElementById('btnLoginNav').classList.add('d-none');
                document.getElementById('btnRegisterNav').classList.add('d-none');
                document.getElementById('userDropdown').classList.remove('d-none');
                document.getElementById('userName').textContent = user.name;

                const dashBtn = document.getElementById('btnDashboard');
                if (user.role === 'seller') {
                    dashBtn.href = '/dashboard/seller';
                } else if (user.role === 'buyer') {
                    dashBtn.href = '/dashboard/buyer';
                } else if (user.role === 'admin') {
                    dashBtn.href = '/dashboard/admin';
                } else {
                    dashBtn.href = '/';
                }
            } else {
                document.getElementById('btnLoginNav').classList.remove('d-none');
                document.getElementById('btnRegisterNav').classList.remove('d-none');
                document.getElementById('userDropdown').classList.add('d-none');
            }
        }

        // Logout handler
        document.getElementById('btnLogout')?.addEventListener('click', async (e) => {
            e.preventDefault();
            try {
                await apiRequest('/logout', 'POST');
            } catch(e) {}
            clearAuth();
            window.location.href = '/';
        });

        // Run on page load
        updateNavAuth();
    </script>
    @yield('scripts')
</body>
</html>
