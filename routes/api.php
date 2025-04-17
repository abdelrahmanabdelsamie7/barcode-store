<?php

use App\Http\Controllers\API\{BrandController,CategoryController,SubCategoryController,ProductController,ColorController,SizeController,ProductColorController,ProductColorImageController,ProductVariantController};

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
]);

Route::match(['post', 'put', 'patch'], 'brands/{id}', [BrandController::class, 'update']);
Route::match(['post', 'put', 'patch'], 'categories/{id}', [CategoryController::class, 'update']);
Route::match(['post', 'put', 'patch'], 'sub-categories/{id}', [SubCategoryController::class, 'update']);
Route::match(['post', 'put', 'patch'], 'products/{id}', [ProductController::class, 'update']);
Route::match(['post', 'put', 'patch'], 'product-color-images/{id}', [ProductColorImageController::class, 'update']);