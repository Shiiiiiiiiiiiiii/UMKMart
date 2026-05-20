<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of orders.
     * - Admin: all orders
     * - Seller: orders for their shop
     * - Buyer: their own orders
     */
    public function index(): JsonResponse
    {
        $user = auth('api')->user();

        if ($user->role === 'admin') {
            $orders = Order::with(['buyer', 'shop', 'orderItems.product'])->latest()->get();
        } elseif ($user->role === 'seller') {
            $shop = $user->shop;
            if (!$shop) {
                return $this->errorResponse('You do not have a shop', 404);
            }
            $orders = Order::with(['buyer', 'orderItems.product'])
                ->where('shop_id', $shop->id)
                ->latest()
                ->get();
        } else {
            $orders = Order::with(['shop', 'orderItems.product'])
                ->where('buyer_id', $user->id)
                ->latest()
                ->get();
        }

        return $this->successResponse($orders, 'Order list');
    }

    /**
     * Store a new order (buyer only).
     */
    public function store(Request $request): JsonResponse
    {
        $user = auth('api')->user();

        if ($user->role !== 'buyer') {
            return $this->errorResponse('Only buyers can place orders', 403);
        }

        $validator = Validator::make($request->all(), [
            'shop_id'              => 'required|exists:shops,id',
            'notes'                => 'nullable|string',
            'items'                => 'required|array|min:1',
            'items.*.product_id'   => 'required|exists:products,id',
            'items.*.quantity'     => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        try {
            $order = DB::transaction(function () use ($request, $user) {
                $totalPrice = 0;
                $orderItems = [];

                // Validate all products belong to the specified shop and have enough stock
                foreach ($request->items as $item) {
                    $product = Product::where('id', $item['product_id'])
                        ->where('shop_id', $request->shop_id)
                        ->first();

                    if (!$product) {
                        throw new \Exception("Product #{$item['product_id']} does not belong to this shop");
                    }

                    if ($product->stock < $item['quantity']) {
                        throw new \Exception("Insufficient stock for product: {$product->name}");
                    }

                    $subtotal = $product->price * $item['quantity'];
                    $totalPrice += $subtotal;

                    $orderItems[] = [
                        'product_id' => $product->id,
                        'quantity'   => $item['quantity'],
                        'price'      => $product->price,
                    ];

                    // Decrease stock
                    $product->decrement('stock', $item['quantity']);
                }

                // Create the order
                $order = Order::create([
                    'buyer_id'    => $user->id,
                    'shop_id'     => $request->shop_id,
                    'total_price' => $totalPrice,
                    'status'      => 'pending',
                    'notes'       => $request->notes,
                ]);

                // Create order items
                foreach ($orderItems as $item) {
                    $order->orderItems()->create($item);
                }

                return $order;
            });

            $order->load(['shop', 'orderItems.product']);

            return $this->successResponse($order, 'Order placed successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Display the specified order.
     */
    public function show(string $id): JsonResponse
    {
        $user = auth('api')->user();
        $order = Order::with(['buyer', 'shop', 'orderItems.product'])->find($id);

        if (!$order) {
            return $this->errorResponse('Order not found', 404);
        }

        // Check authorization
        if ($user->role === 'buyer' && $order->buyer_id !== $user->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        if ($user->role === 'seller') {
            $shop = $user->shop;
            if (!$shop || $order->shop_id !== $shop->id) {
                return $this->errorResponse('Unauthorized', 403);
            }
        }

        return $this->successResponse($order, 'Order details');
    }

    /**
     * Update order status.
     * - Buyer: can upload payment_proof, cancel order
     * - Seller: can confirm paid orders
     * - Admin: can update any status
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = auth('api')->user();
        $order = Order::find($id);

        if (!$order) {
            return $this->errorResponse('Order not found', 404);
        }

        // Buyer actions
        if ($user->role === 'buyer') {
            if ($order->buyer_id !== $user->id) {
                return $this->errorResponse('Unauthorized', 403);
            }

            // Buyer can upload payment proof
            if ($request->hasFile('payment_proof')) {
                $validator = Validator::make($request->all(), [
                    'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                ]);

                if ($validator->fails()) {
                    return $this->errorResponse('Validation error', 422, $validator->errors());
                }

                $path = $request->file('payment_proof')->store('payment_proofs', 'public');
                $order->update([
                    'payment_proof' => $path,
                    'status' => 'paid',
                ]);

                return $this->successResponse($order, 'Payment proof uploaded');
            }

            // Buyer can cancel pending orders
            if ($request->status === 'cancelled' && in_array($order->status, ['pending', 'waiting_payment'])) {
                // Restore stock
                foreach ($order->orderItems as $item) {
                    $item->product->increment('stock', $item->quantity);
                }
                $order->update(['status' => 'cancelled']);

                return $this->successResponse($order, 'Order cancelled');
            }

            return $this->errorResponse('Invalid action', 400);
        }

        // Seller actions
        if ($user->role === 'seller') {
            $shop = $user->shop;
            if (!$shop || $order->shop_id !== $shop->id) {
                return $this->errorResponse('Unauthorized', 403);
            }

            $validator = Validator::make($request->all(), [
                'status' => 'required|in:waiting_payment,confirmed,cancelled',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Validation error', 422, $validator->errors());
            }

            // Seller can move pending -> waiting_payment, paid -> confirmed, or cancel
            if ($request->status === 'cancelled') {
                foreach ($order->orderItems as $item) {
                    $item->product->increment('stock', $item->quantity);
                }
            }

            $order->update(['status' => $request->status]);

            return $this->successResponse($order, 'Order status updated');
        }

        // Admin actions
        if ($user->role === 'admin') {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:pending,waiting_payment,paid,confirmed,cancelled',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Validation error', 422, $validator->errors());
            }

            if ($request->status === 'cancelled' && $order->status !== 'cancelled') {
                foreach ($order->orderItems as $item) {
                    $item->product->increment('stock', $item->quantity);
                }
            }

            $order->update(['status' => $request->status]);

            return $this->successResponse($order, 'Order status updated by admin');
        }

        return $this->errorResponse('Unauthorized', 403);
    }

    /**
     * Delete an order (admin only).
     */
    public function destroy(string $id): JsonResponse
    {
        $user = auth('api')->user();

        if ($user->role !== 'admin') {
            return $this->errorResponse('Only admin can delete orders', 403);
        }

        $order = Order::find($id);

        if (!$order) {
            return $this->errorResponse('Order not found', 404);
        }

        $order->delete();

        return $this->successResponse(null, 'Order deleted');
    }
}
