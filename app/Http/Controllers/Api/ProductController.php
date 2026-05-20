<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of all products (public).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::with('shop');

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by shop
        if ($request->has('shop_id')) {
            $query->where('shop_id', $request->shop_id);
        }

        $products = $query->latest()->paginate(12);

        return $this->successResponse($products, 'Product list');
    }

    /**
     * Store a new product (seller only, via API key).
     */
    public function store(Request $request): JsonResponse
    {
        $user = auth('api')->user();

        if ($user->role !== 'seller') {
            return $this->errorResponse('Only sellers can add products', 403);
        }

        $shop = $user->shop;

        if (!$shop) {
            return $this->errorResponse('You need to create a shop first', 404);
        }

        if ($shop->status !== 'active') {
            return $this->errorResponse('Your shop is not active yet', 403);
        }

        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'category'    => 'nullable|string|max:100',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        $data = $request->only(['name', 'description', 'price', 'stock', 'category']);
        $data['shop_id'] = $shop->id;

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = $path;
        }

        $product = Product::create($data);

        return $this->successResponse($product, 'Product created successfully', 201);
    }

    /**
     * Display the specified product.
     */
    public function show(string $id): JsonResponse
    {
        $product = Product::with('shop')->find($id);

        if (!$product) {
            return $this->errorResponse('Product not found', 404);
        }

        return $this->successResponse($product, 'Product details');
    }

    /**
     * Update the specified product (seller only).
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = auth('api')->user();
        $product = Product::find($id);

        if (!$product) {
            return $this->errorResponse('Product not found', 404);
        }

        $shop = $user->shop;

        // Only the owner seller can update
        if (!$shop || $product->shop_id !== $shop->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $validator = Validator::make($request->all(), [
            'name'        => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'sometimes|numeric|min:0',
            'stock'       => 'sometimes|integer|min:0',
            'category'    => 'nullable|string|max:100',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        $data = $request->only(['name', 'description', 'price', 'stock', 'category']);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = $path;
        }

        $product->update($data);

        return $this->successResponse($product, 'Product updated successfully');
    }

    /**
     * Remove the specified product (seller or admin).
     */
    public function destroy(string $id): JsonResponse
    {
        $user = auth('api')->user();
        $product = Product::find($id);

        if (!$product) {
            return $this->errorResponse('Product not found', 404);
        }

        $shop = $user->shop;

        // Owner seller or admin can delete
        if ($user->role === 'admin' || ($shop && $product->shop_id === $shop->id)) {
            // Delete image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $product->delete();

            return $this->successResponse(null, 'Product deleted successfully');
        }

        return $this->errorResponse('Unauthorized', 403);
    }
}
