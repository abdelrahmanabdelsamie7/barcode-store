<?php
namespace App\Http\Controllers\API;
use App\Models\{Order, Cart, OrderItem};
use App\traits\ResponseJsonTrait;
use App\Http\Requests\OrderRequest;
use App\Http\Controllers\Controller;
class OrderController extends Controller
{
    use ResponseJsonTrait;

    public function __construct()
    {
        $this->middleware('auth:admins')->only('index', 'update');
    }
    public function index()
    {
        $orders = Order::with(['user', 'orderItems.productVariant.product'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($order) {
                if ($order->status !== Order::STATUS_CONFIRMED) {
                    $order->setRelation('orderItems', collect());
                }else{}
                return $order;
            });

        return $this->sendSuccess('Orders retrieved successfully.', $orders);
    }
    public function store(OrderRequest $request)
    {
        $cart = $this->getUserOrVisitorCart();
        if (!$cart || $cart->items->isEmpty()) {
            return $this->sendError('Cart is empty.');
        }
        $orderData = [
            'user_id' => auth('users')->id(),
            'address_id' => $request->address_id,
            'payment_method' => $request->payment_method,
        ];
        $discountCode = $cart->user->discountCodes()->where('is_active', true)->latest()->first();
        if ($discountCode) {
            $discount = $this->getDiscountFromCart($cart);
            $orderData['user_discount_code_id'] = $discountCode->id;
            $orderData['discount_amount'] = $discount;
        }
        $order = Order::create($orderData);
        return $this->sendSuccess('Order created successfully.', $order->load(['orderItems', 'userDiscountCode']));
    }
    public function update($id)
    {
        $order = Order::findOrFail($id);
        if ($order->status !== Order::STATUS_PENDING) {
            return $this->sendError('Order is already confirmed or processed.', 400);
        }
        $cart = Cart::with('items.productVariant.product')
            ->where('user_id', $order->user_id)
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return $this->sendError('Cart not found or empty for this order.', 404);
        }
        foreach ($cart->items as $item) {
            $variant = $item->productVariant;
            if ($variant->quantity < $item->quantity) {
                return $this->sendError("Not enough stock for product variant ID: {$variant->id}", 400);
            }
            $variant->decrement('quantity', $item->quantity);
            OrderItem::create([
                'order_id' => $order->id,
                'product_variant_id' => $variant->id,
                'quantity' => $item->quantity,
                'price' => $variant->product->final_price,
            ]);
        }
        $cart->items()->delete();
        $cart->delete();
        $order->update([
            'status' => Order::STATUS_CONFIRMED,
            'delivered_at' => now()
        ]);
        return $this->sendSuccess('Order confirmed and stock updated.', $order->load('orderItems'));
    }
    protected function getUserOrVisitorCart()
    {
        if (auth('api')->check()) {
            return Cart::with('items.productVariant.product')
                ->where('user_id', auth('api')->id())
                ->first();
        }
        $visitorToken = request()->header('X-Visitor-Token');
        if ($visitorToken) {
            return Cart::with('items.productVariant.product')
                ->where('visitor_token', $visitorToken)
                ->first();
        }
        return null;
    }
    protected function calculateShipping(string $city): float
    {
        return match ($city) {
            'Cairo', 'Giza' => 40,
            'Alexandria', 'Tanta', 'Zagazig' => 50,
            'Sohag', 'Asyut', 'Minya', 'Qena' => 60,
            default => 70,
        };
    }
    private function uploadImage($image)
    {
        if ($image) {
            $imageName = uniqid() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/payment_proofs'), $imageName);
            return asset('uploads/payment_proofs/' . $imageName);
        }
        return null;
    }
}
