<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\{ProductController,SizeController};
Route::get('/home-products', [ProductController::class, 'homeProducts']);
Route::get('/sizes-by-subcategory/{id}', [SizeController::class, 'getSizesBySubCategory']);
