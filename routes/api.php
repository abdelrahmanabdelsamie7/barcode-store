<?php
use App\Http\Controllers\{AuthUserController,AuthAdminController};
use App\Http\Controllers\API\{CategoryController,SubCategoryController,ProductController,ColorController,SizeController,ProductColorController,ProductColorImageController,ProductVariantController,OfferController,CartController,CartItemController,DiscountCampaignController,UserDiscountCodesController,OrderController};

Route::apiResources([
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
    'order'=> OrderController::class,
    'discount-campaigns'=> DiscountCampaignController::class,
    'user-discount-codes'=> UserDiscountCodesController::class,
]);

Route::prefix('user')->group(function () {
    Route::post('/register', [AuthUserController::class, 'register']);
    Route::post('/login', [AuthUserController::class, 'login']);
    Route::post('/logout', [AuthUserController::class, 'logout']);
    Route::get('/getaccount', [AuthUserController::class, 'getaccount']);
});
Route::prefix('admin')->middleware('api')->group(function () {
    Route::post('/login', [AuthAdminController::class, 'login']);

    Route::middleware('auth:admins')->group(function () {
        Route::controller(AuthAdminController::class)->group(function () {
            Route::post('/add-admin', 'addAdmin');
            Route::get('/getaccount', 'getAccount');
            Route::get('/all-admins', 'allAdmins');
            Route::post('/logout', 'logout');
            Route::post('/refresh', 'refresh');
            Route::delete('/delete-admin/{id}', 'deleteAdmin');
            Route::put('/update-password/{id?}', [AuthAdminController::class, 'updatePassword']);
        });
    });
});

Route::get('/home-products', [ProductController::class, 'homeProducts']);
Route::match(['post', 'put', 'patch'], 'categories/{id}', [CategoryController::class, 'update']);
Route::match(['post', 'put', 'patch'], 'sub-categories/{id}', [SubCategoryController::class, 'update']);
Route::match(['post', 'put', 'patch'], 'products/{id}', [ProductController::class, 'update']);
Route::match(['post', 'put', 'patch'], 'product-color-images/{id}', [ProductColorImageController::class, 'update']);
Route::delete('/delete-all', [CartItemController::class, 'destroyAll']);