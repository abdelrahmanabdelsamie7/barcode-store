<?php
use App\Http\Controllers\{AuthUserController,AuthAdminController};
use App\Http\Controllers\API\{BrandController,CategoryController,SubCategoryController,ProductController,ColorController,SizeController,ProductColorController,ProductColorImageController,ProductVariantController,OfferController,CartController,CartItemController};

Route::apiResources([
    'brands' => BrandController::class,
    'categories' => CategoryController::class,
    'sub-categories' => SubCategoryController::class,
    'products' => ProductController::class,
    'colors' => ColorController::class ,
    'sizes' => SizeController::class ,
    'product-colors' => ProductColorController::class ,
    'product-size-quantity' => ProductVariantController::class ,
    'product-color-images' => ProductColorImageController::class,
    'offers'=> OfferController::class,
    'cart'=> CartController::class,
    'cart-item'=> CartItemController::class,
]);

Route::prefix('user')->group(function () {
    Route::post('/register', [AuthUserController::class, 'register']);
    Route::post('/login', [AuthUserController::class, 'login']);
    Route::post('/logout', [AuthUserController::class, 'logout']);
    Route::get('/getaccount', [AuthUserController::class, 'getaccount']);
    Route::post('/password/forgot', [AuthUserController::class, 'forgotPassword']);
    Route::post('/password/reset', [AuthUserController::class, 'resetPassword']);
    Route::delete('/account', [AuthUserController::class, 'deleteAccount']);
    Route::get('/verify-email/{token}', [AuthUserController::class, 'verifyEmail']);
    Route::post('/resend-verification', [AuthUserController::class, 'resendVerification']);
});
Route::prefix( 'admin')->group(function () {
    Route::post('/register', [AuthAdminController::class, 'register']);
    Route::post('/login', [AuthAdminController::class, 'login']);
    Route::post('/logout', [AuthAdminController::class, 'logout']);
    Route::get('/getaccount', [AuthAdminController::class, 'getaccount']);
});

Route::match(['post', 'put', 'patch'], 'brands/{id}', [BrandController::class, 'update']);
Route::match(['post', 'put', 'patch'], 'categories/{id}', [CategoryController::class, 'update']);
Route::match(['post', 'put', 'patch'], 'sub-categories/{id}', [SubCategoryController::class, 'update']);
Route::match(['post', 'put', 'patch'], 'products/{id}', [ProductController::class, 'update']);
Route::match(['post', 'put', 'patch'], 'product-color-images/{id}', [ProductColorImageController::class, 'update']);
Route::delete('cart', [CartController::class, 'deleteItems']);
