<?php
namespace App\Http\Controllers\API;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Models\{Order, Cart, OrderItem, DiscountCampaign, UserDiscountCode};
use App\traits\ResponseJsonTrait;
class OrderController extends Controller
{
    use ResponseJsonTrait;
    public function __construct()
    {
        $this->middleware('auth:admins')->only('index', 'show', 'update', 'changeStatus');
    }
    public function index()
    {
        $orders = Order::with(['user', 'orderItems.productVariant.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->sendSuccess('Orders retrieved successfully.', $orders);
    }
    public function store(OrderRequest $request)
    {
        $cart = $this->getUserOrVisitorCart();
        if (!$cart || $cart->items->isEmpty()) {
            return $this->sendError('Cart is empty.');
        }
        $alreadyHasPendingOrder = Order::where('cart_id', $cart->id)
            ->where('status', Order::STATUS_PENDING)
            ->exists();
        if ($alreadyHasPendingOrder) {
            return $this->sendError('You already have a pending order with the same items.', 400);
        }
        $shipping = $this->calculateShipping($request->city);
        $totalPrice = $cart->total_price;
        $totalWithShipping = $totalPrice + $shipping;
        $visitorToken = request()->header('X-Visitor-Token');
        $user = auth('api')->user();
        $orderData = [
            'id' => Str::uuid(),
            'cart_id' => $cart->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'payment_method' => $request->payment_method,
            'payment_phone' => $request->payment_phone,
            'payment_reference' => $request->payment_reference,
            'total_price' => $totalPrice,
            'total_quantity' => $cart->total_quantity,
            'shipping_cost' => $shipping,
            'status' => Order::STATUS_PENDING,
        ];
        if ($request->hasFile('payment_proof')) {
            $orderData['payment_proof'] = $this->uploadImage($request->file('payment_proof'));
        }
        if ($user) {
            $orderData['user_id'] = $user->id;
        } elseif ($visitorToken) {
            $orderData['visitor_token'] = $visitorToken;
        }
        $codeInput = $request->code;
        $discountCode = null;
        if ($codeInput) {
            $discountCode = UserDiscountCode::where('code', $codeInput)
                ->where('is_used', false)
                ->where(function ($q) use ($user) {
                    $q->whereNull('user_id');
                    if ($user)
                        $q->orWhere('user_id', $user->id);
                })
                ->where(function ($q) use ($visitorToken) {
                    $q->whereNull('visitor_token');
                    if ($visitorToken)
                        $q->orWhere('visitor_token', $visitorToken);
                })
                ->whereHas('campaign', function ($q) {
                    $q->where(function ($q) {
                        $q->whereNull('start_at')->orWhere('start_at', '<=', now());
                    })->where(function ($q) {
                        $q->whereNull('end_at')->orWhere('end_at', '>=', now());
                    });
                })
                ->first();
            if (!$discountCode) {
                return $this->sendError('Invalid or unauthorized discount code.', 403);
            }
            $discountCode->load('campaign');
            $discountAmount = $this->getDiscountFromCart($cart, $discountCode);
            $orderData['user_discount_code_id'] = $discountCode->id;
            $orderData['discount_amount'] = $discountAmount;
            $totalPrice -= $discountAmount;
        }

        $totalWithShipping = $totalPrice + $shipping;
        $order = Order::create($orderData);
        if ($discountCode) {
            $discountCode->update([
                'is_used' => true,
                'used_at' => now(),
                'user_id' => $user?->id ?? $discountCode->user_id,
                'visitor_token' => $visitorToken ?? $discountCode->visitor_token,
            ]);
        }
        return $this->sendSuccess('Order created successfully.', [
            'order' => $order->fresh(['userDiscountCode']),
            'total_price_with_shipping' => $totalWithShipping,
        ]);
    }
    protected function getDiscountFromCart($cart, UserDiscountCode $discountCode = null): float
    {
        if (!$discountCode || !$discountCode->campaign) {
            return 0.00;
        }
        $campaign = $discountCode->campaign;
        $total = $cart->total_price;
        if ($campaign->min_order_value && $total < $campaign->min_order_value) {
            return 0.00;
        }
        if ($campaign->discount_type === 'percent') {
            $discount = ($campaign->discount_value / 100) * $total;
        } elseif ($campaign->discount_type === 'amount') {
            $discount = $campaign->discount_value;
        } else {
            $discount = 0.00;
        }
        return round(min($discount, $total), 2);
    }
    public function update($id)
    {
        $order = Order::findOrFail($id);
        $newStatus = request()->get('status');
        $validStatuses = [
            Order::STATUS_PENDING,
            Order::STATUS_CONFIRMED,
            Order::STATUS_SHIPPED,
            Order::STATUS_DELIVERED,
            Order::STATUS_CANCELLED,
        ];
        if (!in_array($newStatus, $validStatuses)) {
            return $this->sendError('Invalid status provided.', 400);
        }
        DB::beginTransaction();
        try {
            if ($newStatus === Order::STATUS_CONFIRMED) {
                $error = $this->confirmOrder($order);
                if ($error) {
                    DB::rollBack();
                    return $error;
                }
                $campaign = DiscountCampaign::where(function ($q) {
                    $q->whereNull('start_at')->orWhere('start_at', '<=', now());
                })
                    ->where(function ($q) {
                        $q->whereNull('end_at')->orWhere('end_at', '>=', now());
                    })
                    ->where(function ($q) use ($order) {
                        $q->whereNull('min_order_value')
                            ->orWhere('min_order_value', '<=', $order->total_price);
                    })
                    ->latest()
                    ->first();
                if ($campaign) {
                    if ($order->user_id) {
                        $alreadyHasCode = UserDiscountCode::where('user_id', $order->user_id)
                            ->where('campaign_id', $campaign->id)
                            ->exists();

                        if (!$alreadyHasCode) {
                            UserDiscountCode::create([
                                'user_id' => $order->user_id,
                                'campaign_id' => $campaign->id,
                                'code' => strtoupper(Str::random(10)),
                            ]);
                        }
                    } elseif ($order->visitor_token) {
                        $alreadyHasCode = UserDiscountCode::where('visitor_token', $order->visitor_token)
                            ->where('campaign_id', $campaign->id)
                            ->exists();
                        if (!$alreadyHasCode) {
                            UserDiscountCode::create([
                                'visitor_token' => $order->visitor_token,
                                'campaign_id' => $campaign->id,
                                'code' => strtoupper(Str::random(10)),
                            ]);
                        }
                    }
                }
            }
            if ($newStatus === Order::STATUS_DELIVERED) {
                $order->delivered_at = now();
            }
            if ($newStatus === Order::STATUS_SHIPPED) {
                $order->shipped_at = now();
            }
            $order->status = $newStatus;
            $order->save();
            DB::commit();
            return $this->sendSuccess("Order status updated to {$newStatus}.", $order->load('orderItems'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Something went wrong while updating the order status.', 500);
        }
    }
    private function confirmOrder(Order $order)
    {
        if ($order->status !== Order::STATUS_PENDING) {
            return $this->sendError('Only pending orders can be confirmed.', 400);
        }
        $cart = Cart::with('items.productVariant.product')
            ->when($order->user_id, fn($q) => $q->where('user_id', $order->user_id))
            ->when($order->visitor_token, fn($q) => $q->where('visitor_token', $order->visitor_token))
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
        return null;
    }
    public function show($id)
    {
        $order = Order::with(['user', 'orderItems.productVariant.product', 'userDiscountCode'])
            ->findOrFail($id);

        return $this->sendSuccess('Order retrieved successfully.', $order);
    }
    public function myOrders()
    {
        if (auth('api')->check()) {
            $orders = Order::with('orderItems.productVariant.product')
                ->where('user_id', auth('api')->id())
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $visitorToken = request()->header('X-Visitor-Token');
            if (!$visitorToken) {
                return $this->sendError('No visitor token provided.', 400);
            }

            $orders = Order::with('orderItems.productVariant.product')
                ->byVisitor($visitorToken)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return $this->sendSuccess('Orders retrieved successfully.', $orders);
    }
    protected function getUserOrVisitorCart()
    {
        if (auth('api')->check()) {
            return Cart::with('items.productVariant.product', 'user')
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
            'Cairo', 'Giza' => 80,
            'Alexandria', 'Tanta', 'Zagazig' => 100,
            'Sohag', 'Asyut', 'Minya', 'Qena' => 60,
            default => 70,
        };
    }
    private function uploadImage($image)
    {
        if ($image) {
            $imageName = uniqid() . '_' . Str::slug(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/payment_proofs'), $imageName);
            return asset('uploads/payment_proofs/' . $imageName);
        }

        return null;
    }
}