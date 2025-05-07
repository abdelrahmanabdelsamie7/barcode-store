<?php
namespace App\Http\Controllers\API;
use App\Models\Cart;
use App\traits\ResponseJsonTrait;
use App\Http\Controllers\Controller;

class CartController extends Controller
{
    use ResponseJsonTrait;
    protected $user;
    public function __construct()
    {
        $this->user = auth('api')->user();
    }
    public function index()
    {
        $cart = Cart::where('user_id', $this->user->id)
            ->with('cartItems.productVariant.product_color.product')
            ->first();
        if (!$cart || optional($cart->cartItems)->isEmpty()) {
            return $this->sendSuccess("Cart is empty!");
        }
        return $this->sendSuccess('Cart Retrieved Successfully!', $cart);
    }
    public function store()
    {
        $cart = Cart::firstOrCreate(['user_id' => $this->user->id]);
        $cart->updateTotalPrice();
        return $this->sendSuccess('Cart Created Successfully!', $cart, 201);
    }
    public function deleteItems()
    {
        $cart = Cart::where('user_id', $this->user->id)->firstOrFail();
        $cart->cartItems()->delete();
        $cart->updateTotalPrice();
        return $this->sendSuccess('Cart items deleted successfully!');
    }
}
