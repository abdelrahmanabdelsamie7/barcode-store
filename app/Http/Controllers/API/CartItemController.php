<?php
namespace App\Http\Controllers\API;
use App\Models\{Cart, CartItem, ProductVariant};
use App\traits\ResponseJsonTrait;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartItemController extends Controller
{
    use ResponseJsonTrait;
    public function store(Request $request)
    {
        $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'nullable|integer|min:1'
        ]);

        $user = auth('api')->user();
        $cart = $user ? $user->cart : Cart::firstOrCreate(['user_id' => null]);
        $productVariant = ProductVariant::with('product_color.product')->findOrFail($request->product_variant_id);
        $price = $productVariant->product_color->product->final_price;
        if ($price === null) {
            return $this->sendError('Product price is not available', 400);
        }
        $quantity = $request->quantity ?? 1;

        if ($quantity > $productVariant->quantity) {
            return $this->sendError('Insufficient stock available', 400);
        }

        $cartItem = CartItem::where([
            'cart_id' => $cart->id,
            'product_variant_id' => $productVariant->id,
        ])->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $quantity;

            if ($newQuantity > $productVariant->quantity) {
                return $this->sendError('Insufficient stock available', 400);
            }

            $cartItem->update([
                'quantity' => $newQuantity,
                'price' => $price
            ]);
        } else {
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_variant_id' => $productVariant->id,
                'quantity' => $quantity,
                'price' => $price
            ]);
        }

        $cart->updateTotalPrice();

        return $this->sendSuccess('Product added to cart successfully', $cartItem, 201);
    }
    public function update(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
        $user = auth('api')->user();
        if ($user && $cartItem->cart->user_id !== $user->id) {
            return $this->sendError('Unauthorized: You do not own this cart item', 403);
        }
        if ($request->quantity > $cartItem->productVariant->quantity) {
            return $this->sendError('Insufficient stock available', 400);
        }
        $cartItem->updateQuantity($request->quantity);
        return $this->sendSuccess('Cart item quantity updated successfully', $cartItem);
    }
    public function destroy(CartItem $cartItem)
    {
        $user = auth('api')->user();
        if ($user && $cartItem->cart->user_id !== $user->id) {
            return $this->sendError('Unauthorized: You do not own this cart item', 403);
        }
        $cart = $cartItem->cart;
        $cartItem->delete();
        $cart->updateTotalPrice();
        return $this->sendSuccess('Cart item removed successfully');
    }
}
