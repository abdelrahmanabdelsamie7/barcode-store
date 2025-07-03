<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\{Product, HomeProduct};
use Carbon\Carbon;
class RefreshHomeProducts extends Command
{
    protected $signature = 'home:refresh-products';
    protected $description = 'Refresh home section with 10 random products daily';
    public function handle()
    {
        $today = Carbon::today();
        HomeProduct::where('date', $today)->delete();
        $products = Product::where('status', 'active')->inRandomOrder()->limit(10)->get();
        foreach ($products as $product) {
            HomeProduct::create([
                'product_id' => $product->id,
                'date' => $today,
            ]);
        }
        $this->info('Home products refreshed successfully!');
    }
}
