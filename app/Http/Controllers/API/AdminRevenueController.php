<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Order;
class AdminRevenueController extends Controller
{
    public function tenPercentOfDeliveredOrders()
    {
        $deliveredOrders = Order::where('status', 'Delivered')->get();
        $totalCommission = $deliveredOrders->sum(function ($order) {
            return $order->total_price * 0.10;
        });
        return response()->json([
            'message' => '10% revenue from delivered orders calculated successfully',
            'total_10_percent' => round($totalCommission, 2),
        ]);
    }
}