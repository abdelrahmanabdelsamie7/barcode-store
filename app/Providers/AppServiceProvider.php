<?php
namespace App\Providers;
use App\Models\{Product,SubCategory};
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }
    public function boot(): void
    {
       Relation::morphMap([
        'product' => Product::class,
        'sub_category' => SubCategory::class,
    ]);
    }
}
