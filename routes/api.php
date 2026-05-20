<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| UMKMart API Routes
| Base URL: /api
|
*/

// ──────────────────────────────────────────────
// Public Routes (No authentication required)
// ──────────────────────────────────────────────
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public product browsing
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/shops/{id}', [ShopController::class, 'show']);

// ──────────────────────────────────────────────
// Protected Routes (JWT Authentication required)
// ──────────────────────────────────────────────
Route::middleware('auth:api')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/me', [AuthController::class, 'me']);

    // ──────────────────────────────────────────
    // Admin Routes
    // ──────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/shops', [ShopController::class, 'index']);
        Route::put('/admin/shops/{id}', [ShopController::class, 'update']);
        Route::delete('/admin/shops/{id}', [ShopController::class, 'destroy']);
        Route::get('/admin/orders', [OrderController::class, 'index']);
        Route::put('/admin/orders/{id}', [OrderController::class, 'update']);
        Route::delete('/admin/orders/{id}', [OrderController::class, 'destroy']);
    });

    // ──────────────────────────────────────────
    // Seller Routes
    // ──────────────────────────────────────────
    Route::middleware('role:seller')->group(function () {
        // Shop management
        Route::post('/seller/shops', [ShopController::class, 'store']);
        Route::get('/seller/my-shop', [ShopController::class, 'myShop']);
        Route::put('/seller/my-shop', [ShopController::class, 'update']);
        Route::post('/seller/my-shop/regenerate-key', [ShopController::class, 'regenerateApiKey']);

        // Product management (also requires API key)
        Route::middleware('api_key')->group(function () {
            Route::post('/seller/products', [ProductController::class, 'store']);
            Route::put('/seller/products/{id}', [ProductController::class, 'update']);
            Route::delete('/seller/products/{id}', [ProductController::class, 'destroy']);
        });

        // Order management for seller
        Route::get('/seller/orders', [OrderController::class, 'index']);
        Route::get('/seller/orders/{id}', [OrderController::class, 'show']);
        Route::put('/seller/orders/{id}', [OrderController::class, 'update']);
    });

    // ──────────────────────────────────────────
    // Buyer Routes
    // ──────────────────────────────────────────
    Route::middleware('role:buyer')->group(function () {
        Route::post('/buyer/orders', [OrderController::class, 'store']);
        Route::get('/buyer/orders', [OrderController::class, 'index']);
        Route::get('/buyer/orders/{id}', [OrderController::class, 'show']);
        Route::put('/buyer/orders/{id}', [OrderController::class, 'update']);
    });
});
