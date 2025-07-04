<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Order;
class AdminRevenueController extends Controller
{
    public function tenPercentOfDeliveredOrders()
    {
        $deliveredOrders = Order::where('status', Order::STATUS_DELIVERED)->get();
        $totalCommission = $deliveredOrders->sum(function ($order) {
            return $order->total_price * 0.10;
        });
        return response()->json([
            'message' => '10% revenue from Delivered orders calculated successfully',
            'total_10_percent' => round($totalCommission, 2),
            'total_orders_count' => $deliveredOrders->count(),
        ]);
    }
}
