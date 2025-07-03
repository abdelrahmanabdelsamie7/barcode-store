<?php
namespace App\Http\Controllers\API;
use App\Models\{Cart, ProductVariant};
use App\traits\ResponseJsonTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CartItemRequest;

class CartItemController extends Controller
{
    use ResponseJsonTrait;
    private function getCart(Request $request)
    {
        if (auth('api')->check()) {
            return Cart::firstOrCreate(['user_id' => auth('api')->id()]);
        } else {
            $visitorToken = $request->cookie('visitor_token');
            if (!$visitorToken) {
                return null;
            }
            return Cart::firstOrCreate(['visitor_token' => $visitorToken]);
        }
    }
    public function store(CartItemRequest $request)
    {
        $cart = $this->getCart($request);
        if (!$cart) {
            return $this->sendError('Visitor token is missing.', 401);
        }
        $productVariant = ProductVariant::findOrFail($request->product_variant_id);
        $existing = $cart->items()->where('product_variant_id', $request->product_variant_id)->first();
        $currentQuantity = $existing ? $existing->quantity : 0;
        $newQuantity = $currentQuantity + $request->quantity;
        if ($newQuantity > $productVariant->quantity) {
            return $this->sendError("Quantity requested exceeds available stock.", 402);
        }
        if ($existing) {
            $existing->update(['quantity' => $newQuantity]);
        } else {
            $cart->items()->create($request->validated());
        }
        return $this->sendSuccess("Item added to cart successfully.", [], 201);
    }
    public function update(Request $request, $cartItemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);
        $cart = $this->getCart($request);
        if (!$cart) {
            return $this->sendError('Visitor token is missing.', 401);
        }
        $cartItem = $cart->items()->where('id', $cartItemId)->first();
        if (!$cartItem) {
            return $this->sendError('Cart item not found.', 404);
        }
        $productVariant = ProductVariant::findOrFail($cartItem->product_variant_id);
        if ($request->quantity > $productVariant->quantity) {
            return $this->sendError('Quantity requested exceeds available stock.', 400);
        }
        $cartItem->update(['quantity' => $request->quantity]);
        return $this->sendSuccess('Cart item quantity updated successfully.', []);
    }
    public function destroy(Request $request, $cartItemId)
    {
        $cart = $this->getCart($request);
        if (!$cart) {
            return $this->sendError('Visitor token is missing.', 401);
        }
        $cartItem = $cart->items()->where('id', $cartItemId)->first();
        if (!$cartItem) {
            return $this->sendError('Cart item not found.', 404);
        }
        $cartItem->delete();
        return $this->sendSuccess('Item removed from cart successfully.');
    }
    public function destroyAll(Request $request)
    {
        $cart = $this->getCart($request);
        if (!$cart) {
            return $this->sendError('Visitor token is missing.', 401);
        }
        $cart->items()->delete();
        return $this->sendSuccess('All items removed from cart successfully.');
    }
}