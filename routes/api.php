<?php

use App\Http\Controllers\API\{BrandController,CategoryController,SubCategoryController};

Route::apiResources([
    'brands' => BrandController::class,
    'categories' => CategoryController::class,
    'sub-categories' => SubCategoryController::class,
]);

Route::match(['post', 'put', 'patch'], 'brands/{id}', [BrandController::class, 'update']);
Route::match(['post', 'put', 'patch'], 'categories/{id}', [CategoryController::class, 'update']);
Route::match(['post', 'put', 'patch'], 'sub-categories/{id}', [SubCategoryController::class, 'update']);