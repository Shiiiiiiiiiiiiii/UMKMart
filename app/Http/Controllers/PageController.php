<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Home page - Product listing.
     */
    public function home(Request $request)
    {
        $query = Product::with('shop')->whereHas('shop', function ($q) {
            $q->where('status', 'active');
        });

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        $products = $query->latest()->paginate(12);
        $categories = Product::distinct()->pluck('category')->filter();

        return view('home', compact('products', 'categories'));
    }

    /**
     * Product detail page.
     */
    public function productDetail(string $id)
    {
        $product = Product::with('shop')->findOrFail($id);
        $relatedProducts = Product::where('shop_id', $product->shop_id)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        return view('product-detail', compact('product', 'relatedProducts'));
    }

    /**
     * Login page.
     */
    public function loginPage()
    {
        return view('auth.login');
    }

    /**
     * Register page.
     */
    public function registerPage()
    {
        return view('auth.register');
    }

    /**
     * Buyer dashboard.
     */
    public function buyerDashboard()
    {
        return view('dashboard.buyer');
    }

    /**
     * Seller dashboard.
     */
    public function sellerDashboard()
    {
        return view('dashboard.seller');
    }

    /**
     * Admin dashboard.
     */
    public function adminDashboard()
    {
        return view('dashboard.admin');
    }
}
