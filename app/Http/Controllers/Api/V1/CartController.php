<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\Product;

/**
 * @group Cart
 *
 * APIs for managing the user's shopping cart
 */
class CartController extends Controller
{
    /**
     * Get user cart
     *
     * Retrieves all items in the authenticated user's cart.
     * 
     * @authenticated
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "product_id": 5,
     *       "product_title": "Sneakers",
     *       "quantity": 2,
     *       "price": 5000,
     *       "shipping_type": "express",
     *       "image": "http://example.com/img.jpg"
     *     }
     *   ]
     * }
     */
    public function index(Request $request)
    {
        $carts = Cart::with('product')->where('users_id', $request->user()->id)->get();

        return CartResource::collection($carts);
    }

    /**
     * Add item to cart
     *
     * @authenticated
     *
     * @bodyParam product_id int required The ID of the product. Example: 1
     * @bodyParam quantity int required The quantity to add. Example: 1
     * @bodyParam shipping_type string The shipping type (express or standard). Example: express
     *
     * @response 200 {
     *   "message": "Item added to cart",
     *   "data": { "id": 1 }
     * }
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'shipping_type' => 'nullable|string'
        ]);

        $product = Product::findOrFail($request->product_id);
        $user = $request->user();

        // Check if already in cart
        $cartItem = Cart::where('users_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            $cartItem = Cart::create([
                'users_id' => $user->id,
                'product_id' => $product->id,
                'product_title' => $product->name,
                'quantity' => $request->quantity,
                'price' => $product->express_price ?? 0,
                'shipping_type' => $request->shipping_type ?? 'express',
                'image' => $product->first_image_url
            ]);
        }

        return response()->json([
            'message' => 'Item added to cart',
            'data' => new CartResource($cartItem)
        ]);
    }

    /**
     * Update cart item quantity
     *
     * @authenticated
     *
     * @urlParam cart required The ID of the cart item. Example: 1
     * @bodyParam quantity int required The new quantity. Example: 3
     *
     * @response 200 {
     *   "message": "Cart updated"
     * }
     */
    public function update(Request $request, Cart $cart)
    {
        if ($cart->users_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cart->quantity = $request->quantity;
        $cart->save();

        return response()->json([
            'message' => 'Cart updated',
            'data' => new CartResource($cart)
        ]);
    }

    /**
     * Remove item from cart
     *
     * @authenticated
     *
     * @urlParam cart required The ID of the cart item. Example: 1
     *
     * @response 200 {
     *   "message": "Item removed from cart"
     * }
     */
    public function destroy(Request $request, Cart $cart)
    {
        if ($cart->users_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $cart->delete();

        return response()->json(['message' => 'Item removed from cart']);
    }
}
