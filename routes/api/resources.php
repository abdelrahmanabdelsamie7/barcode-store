<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\{
    CategoryController,
    SubCategoryController,
    ProductController,
    ColorController,
    SizeController,
    ProductColorController,
    ProductColorImageController,
    ProductVariantController,
    OfferController,
    CartController,
    CartItemController,
    DiscountCampaignController,
    UserDiscountCodesController,
    OrderController,
    WishlistController
};
Route::apiResources([
    'categories' => CategoryController::class,
    'sub-categories' => SubCategoryController::class,
    'products' => ProductController::class,
    'colors' => ColorController::class,
    'sizes' => SizeController::class,
    'product-colors' => ProductColorController::class,
    'product-size-quantity' => ProductVariantController::class,
    'product-color-images' => ProductColorImageController::class,
    'offers' => OfferController::class,
    'cart' => CartController::class,
    'cart-item' => CartItemController::class,
    'order' => OrderController::class,
    'wishlist' => WishlistController::class,
    'discount-campaigns' => DiscountCampaignController::class,
    'user-discount-codes' => UserDiscountCodesController::class,
]);


Route::match(['post', 'put', 'patch'], 'categories/{id}', [CategoryController::class, 'update']);
Route::match(['post', 'put', 'patch'], 'sub-categories/{id}', [SubCategoryController::class, 'update']);
Route::match(['post', 'put', 'patch'], 'products/{id}', [ProductController::class, 'update']);
Route::match(['post', 'put', 'patch'], 'product-color-images/{id}', [ProductColorImageController::class, 'update']);
Route::get('/my-orders', [OrderController::class, 'myOrders']);
Route::delete('/delete-all', [CartItemController::class, 'destroyAll']);