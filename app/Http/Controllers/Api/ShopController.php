<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ShopController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of shops (admin only).
     */
    public function index(): JsonResponse
    {
        $shops = Shop::with('user')->latest()->get();

        return $this->successResponse($shops, 'List of all shops');
    }

    /**
     * Store a new shop (seller only).
     */
    public function store(Request $request): JsonResponse
    {
        $user = auth('api')->user();

        if ($user->role !== 'seller') {
            return $this->errorResponse('Only sellers can create shops', 403);
        }

        // Check if seller already has a shop
        if ($user->shop) {
            return $this->errorResponse('You already have a shop', 409);
        }

        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'address'     => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        $shop = Shop::create([
            'user_id'     => $user->id,
            'name'        => $request->name,
            'description' => $request->description,
            'address'     => $request->address,
            'api_key'     => Str::random(64),
            'status'      => 'pending',
        ]);

        return $this->successResponse($shop, 'Shop created successfully', 201);
    }

    /**
     * Display the specified shop.
     */
    public function show(string $id): JsonResponse
    {
        $shop = Shop::with(['user', 'products'])->find($id);

        if (!$shop) {
            return $this->errorResponse('Shop not found', 404);
        }

        return $this->successResponse($shop, 'Shop details');
    }

    /**
     * Update the specified shop (owner or admin).
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = auth('api')->user();
        $shop = Shop::find($id);

        if (!$shop) {
            return $this->errorResponse('Shop not found', 404);
        }

        // Only shop owner or admin can update
        if ($shop->user_id !== $user->id && $user->role !== 'admin') {
            return $this->errorResponse('Unauthorized', 403);
        }

        $validator = Validator::make($request->all(), [
            'name'        => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'address'     => 'nullable|string',
            'status'      => 'sometimes|in:pending,active,banned',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        $data = $request->only(['name', 'description', 'address']);

        // Only admin can change status
        if ($user->role === 'admin' && $request->has('status')) {
            $data['status'] = $request->status;
        }

        $shop->update($data);

        return $this->successResponse($shop, 'Shop updated successfully');
    }

    /**
     * Remove the specified shop (admin only).
     */
    public function destroy(string $id): JsonResponse
    {
        $user = auth('api')->user();

        if ($user->role !== 'admin') {
            return $this->errorResponse('Only admin can delete shops', 403);
        }

        $shop = Shop::find($id);

        if (!$shop) {
            return $this->errorResponse('Shop not found', 404);
        }

        $shop->delete();

        return $this->successResponse(null, 'Shop deleted successfully');
    }

    /**
     * Get my shop (seller only).
     */
    public function myShop(): JsonResponse
    {
        $user = auth('api')->user();
        $shop = $user->shop;

        if (!$shop) {
            return $this->errorResponse('You do not have a shop yet', 404);
        }

        $shop->load('products');

        return $this->successResponse($shop, 'Your shop details');
    }

    /**
     * Regenerate API key for shop.
     */
    public function regenerateApiKey(): JsonResponse
    {
        $user = auth('api')->user();
        $shop = $user->shop;

        if (!$shop) {
            return $this->errorResponse('You do not have a shop', 404);
        }

        $shop->update(['api_key' => Str::random(64)]);

        return $this->successResponse(['api_key' => $shop->api_key], 'API key regenerated');
    }
}
