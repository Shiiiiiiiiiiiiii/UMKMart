<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

// Home - Product Listing
Route::get('/', [PageController::class, 'home'])->name('home');

// Product Detail
Route::get('/product/{id}', [PageController::class, 'productDetail'])->name('product.detail');

// Auth Pages
Route::get('/login', [PageController::class, 'loginPage'])->name('login');
Route::get('/register', [PageController::class, 'registerPage'])->name('register');

// Dashboard Pages (protected via JS/JWT on frontend)
Route::get('/dashboard/buyer', [PageController::class, 'buyerDashboard'])->name('dashboard.buyer');
Route::get('/dashboard/seller', [PageController::class, 'sellerDashboard'])->name('dashboard.seller');
Route::get('/dashboard/admin', [PageController::class, 'adminDashboard'])->name('dashboard.admin');
