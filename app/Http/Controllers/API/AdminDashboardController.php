<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\{Order,Product,ProductVariant};
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class AdminDashboardController extends Controller
{
    public function overview()
    {
        $totalOrders = Order::count();
        $totalRevenue = Order::where('status', 'Confirmed')->sum('total_price');  
        $todayOrders = Order::whereDate('created_at', Carbon::today())->count();
        $totalProducts = Product::count();
        $lowStockVariants = ProductVariant::with('product')
            ->where('quantity', '<', 2)
            ->get();
        $topSelling = DB::table('order_items')
            ->select('product_variant_id', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('product_variant_id')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $variant =ProductVariant::with(['product', 'size'])->find($item->product_variant_id);
                return [
                    'variant_id' => $item->product_variant_id,
                    'product' => $variant?->product?->title,
                    'size' => $variant?->size?->name,
                    'total_sold' => $item->total_sold,
                ];
            });
        return response()->json([
            'total_orders' => $totalOrders,
            'total_revenue' => $totalRevenue,
            'today_orders' => $todayOrders,
            'total_products' => $totalProducts,
            'low_stock_variants' => $lowStockVariants,
            'top_selling_products' => $topSelling,
        ]);
    }
}