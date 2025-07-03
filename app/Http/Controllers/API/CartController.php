<?php
namespace App\Http\Controllers\API;
use App\Models\{Cart};
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\traits\ResponseJsonTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\CartItemResource;

class CartController extends Controller
{
    use ResponseJsonTrait;
    public function index(Request $request)
    {
        $isLight = $request->query('light') === 'true';

        if (auth('api')->check()) {
            $cart = Cart::with([
                'items.productVariant' => function ($q) {
                    $q->with(['product_color', 'size', 'product']);
                }
            ])->firstOrCreate(['user_id' => auth('api')->id()]);
        } else {
            $visitorToken = $request->cookie('visitor_token') ?? Str::uuid()->toString();

            $cart = Cart::with(relations: [
                'items.productVariant' => function ($q) {
                    $q->with(['product_color', 'size', 'product']);
                }
            ])->firstOrCreate(['visitor_token' => $visitorToken]);

            return $this->sendSuccess('Your Cart Retrieved Successfully', [
                'cart' => [
                    'id' => $cart->id,
                    'user_id' => $cart->user_id,
                    'visitor_token' => $cart->visitor_token,
                    'total_quantity' => $cart->total_quantity,
                    'total_price' => $cart->total_price,
                    'items' => CartItemResource::collection($cart->items)->additional(['light' => $isLight]),
                ],
                'cart_id' => $cart->id
            ])->cookie('visitor_token', $visitorToken, 60 * 24 * 30);
        }

        return $this->sendSuccess('Your Cart Retrieved Successfully', [
            'cart' => [
                'id' => $cart->id,
                'user_id' => $cart->user_id,
                'visitor_token' => $cart->visitor_token,
                'total_quantity' => $cart->total_quantity,
                'total_price' => $cart->total_price,
                'items' => CartItemResource::collection($cart->items)->additional(['light' => $isLight]),
            ],
            'cart_id' => $cart->id
        ]);
    }
}
