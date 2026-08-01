<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Cart;
use Illuminate\Support\Facades\DB;

/**
 * @group Orders
 *
 * APIs for managing user orders
 */
class OrderController extends Controller
{
    /**
     * List orders
     *
     * Retrieves all orders for the authenticated user.
     * 
     * @authenticated
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "masked_order_id": "ORD-ABC12345",
     *       "total": 15000,
     *       "status": "pending"
     *     }
     *   ]
     * }
     */
    public function index(Request $request)
    {
        $orders = Order::with('items.product')->where('user_id', $request->user()->id)->latest()->get();

        return OrderResource::collection($orders);
    }

    /**
     * Get order details
     *
     * @authenticated
     * @urlParam order required The ID of the order. Example: 1
     * 
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "items": []
     *   }
     * }
     */
    public function show(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $order->load('items.product');
        return new OrderResource($order);
    }

    /**
     * Checkout
     *
     * Converts the user's current cart into an order.
     *
     * @authenticated
     *
     * @bodyParam payment_method string required The payment method (e.g., card, momo, cod). Example: momo
     * @bodyParam customer_phone string required Contact phone for the order. Example: 0780000000
     * @bodyParam customer_name string required Name for the order. Example: John Doe
     *
     * @response 201 {
     *   "message": "Order created successfully",
     *   "data": { "id": 1 }
     * }
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string',
            'customer_phone' => 'required|string',
            'customer_name' => 'required|string'
        ]);

        $user = $request->user();
        $cartItems = Cart::with('product')->where('users_id', $user->id)->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        DB::beginTransaction();

        try {
            $total = 0;
            foreach ($cartItems as $item) {
                $total += $item->price * $item->quantity;
            }

            $order = Order::create([
                'user_id' => $user->id,
                'customer_name' => $request->customer_name,
                'customer_email' => $user->email,
                'customer_phone' => $request->customer_phone,
                'total' => $total,
                'payment_method' => $request->payment_method,
                'status' => Order::STATUS_PENDING,
            ]);

            foreach ($cartItems as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ]);
            }

            // Clear cart
            Cart::where('users_id', $user->id)->delete();

            DB::commit();

            $order->load('items.product');

            return response()->json([
                'message' => 'Order created successfully',
                'data' => new OrderResource($order)
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Checkout failed', 'error' => $e->getMessage()], 500);
        }
    }
}
