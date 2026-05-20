# 🛒 UMKMart — Marketplace UMKM Lokal

> Platform marketplace berbasis REST API untuk mendukung UMKM lokal Indonesia.
> Dibangun dengan Laravel + JWT Authentication.

[![Laravel](https://img.shields.io/badge/Laravel-12.x-red?logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue?logo=php)](https://php.net)
[![JWT](https://img.shields.io/badge/Auth-JWT-green)](https://jwt.io)
[![SDGs](https://img.shields.io/badge/SDGs-8%20%7C%2010%20%7C%2017-orange)](https://sdgs.un.org)

---

## 🌍 Keterkaitan SDGs

| SDG | Keterangan |
|-----|-----------|
| **SDG 8** | Mendukung pertumbuhan ekonomi dan pekerjaan layak bagi pelaku UMKM |
| **SDG 10** | Memperkecil kesenjangan dengan memberi akses pasar digital untuk usaha kecil |
| **SDG 17** | Membangun ekosistem kemitraan digital antara UMKM dan konsumen |

---

## ⚙️ Tech Stack

- **Backend:** Laravel 12, PHP 8.2+
- **Auth:** JWT (`php-open-source-saver/jwt-auth`) — Basic Auth + API Key
- **Database:** MySQL
- **Frontend:** Laravel Blade + Bootstrap 5

---

## 👥 Role Pengguna

| Role | Akses |
|------|-------|
| `admin` | Kelola semua toko & order |
| `seller` | CRUD produk, kelola order masuk |
| `buyer` | Browse produk, buat order, upload bukti bayar |

---

## 🚀 Cara Instalasi

```bash
# 1. Clone repository
git clone https://github.com/username/umkmart.git
cd umkmart

# 2. Install dependencies
composer install

# 3. Copy dan setup environment
cp .env.example .env
# Edit .env — isi DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 4. Generate app key & JWT secret
php artisan key:generate
php artisan jwt:secret

# 5. Jalankan migrasi + seeder
php artisan migrate --seed

# 6. Jalankan server
php artisan serve
```

Aplikasi berjalan di: `http://localhost:8000`

---

## 📡 API Endpoints

### Auth (Public)
| Method | Endpoint | Keterangan |
|--------|----------|-----------|
| POST | `/api/register` | Daftar akun baru |
| POST | `/api/login` | Login → dapat JWT token |
| POST | `/api/logout` | Logout (butuh token) |
| POST | `/api/refresh` | Refresh JWT token |
| GET | `/api/me` | Info user yang login |

### Produk (Public)
| Method | Endpoint | Keterangan |
|--------|----------|-----------|
| GET | `/api/products` | Daftar semua produk |
| GET | `/api/products/{id}` | Detail produk |
| GET | `/api/shops/{id}` | Detail toko |

### Seller (JWT + API Key)
| Method | Endpoint | Keterangan |
|--------|----------|-----------|
| POST | `/api/seller/shops` | Buat toko |
| GET | `/api/seller/my-shop` | Info toko sendiri |
| POST | `/api/seller/products` | Tambah produk |
| PUT | `/api/seller/products/{id}` | Edit produk |
| DELETE | `/api/seller/products/{id}` | Hapus produk |
| GET | `/api/seller/orders` | Daftar order masuk |
| PUT | `/api/seller/orders/{id}` | Update status order |

### Buyer (JWT)
| Method | Endpoint | Keterangan |
|--------|----------|-----------|
| POST | `/api/buyer/orders` | Buat order baru |
| GET | `/api/buyer/orders` | Riwayat order |
| PUT | `/api/buyer/orders/{id}` | Upload bukti bayar |

### Admin (JWT)
| Method | Endpoint | Keterangan |
|--------|----------|-----------|
| GET | `/api/admin/shops` | Semua toko |
| PUT | `/api/admin/shops/{id}` | Update status toko |
| GET | `/api/admin/orders` | Semua order |

---

## 🔐 Autentikasi

### 1. Basic Auth (JWT)
```
POST /api/login
Content-Type: application/json

{
  "email": "seller@example.com",
  "password": "password"
}
```
Gunakan token yang didapat sebagai `Authorization: Bearer <token>`

### 2. API Key (Seller)
Untuk endpoint seller products, tambahkan header:
```
X-API-KEY: <api_key_toko>
```
API Key bisa dilihat/regenerate di endpoint `/api/seller/my-shop`

---

## 🗄️ Struktur Database

```
users ──< shops ──< products
                └──< orders ──< order_items
users (buyer) ──<──┘
```

---

## 📦 Akun Demo (Seeder)

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@gmail.com | password |
| Seller | seller@gmail.com | password |
| Buyer | buyer@gmail.com | password |

---

## 📄 Dokumentasi API

Import file Postman collection yang tersedia di folder `/docs`:
```
docs/UMKMart.postman_collection.json
```

---

## 👨‍💻 Tim Pengembang

> Proyek ini dikembangkan sebagai bagian dari UAS Mata Kuliah Pemrograman API
> dengan tema SDGs — Ekonomi.

---

## 📝 Lisensi

MIT License
