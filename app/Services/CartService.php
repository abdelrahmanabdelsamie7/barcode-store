<?php
namespace App\Services;
use App\Models\Cart;
class CartService
{
    public function transferVisitorCartToUser($visitorToken, $userId)
    {
        if (!$visitorToken || !$userId) return;
        $visitorCart = Cart::with('items')->where('visitor_token', $visitorToken)->first();
        if (!$visitorCart || $visitorCart->items->isEmpty()) return;
        $userCart = Cart::firstOrCreate(['user_id' => $userId]);
        foreach ($visitorCart->items as $item) {
            $existingItem = $userCart->items()->where('product_variant_id', $item->product_variant_id)->first();
            if ($existingItem) {
                $existingItem->increment('quantity', $item->quantity);
            } else {
                $userCart->items()->create([
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                ]);
            }
        }
        $visitorCart->items()->delete();
        $visitorCart->delete();
    }
}