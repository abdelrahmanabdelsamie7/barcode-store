<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductColorRequest;
use App\Models\ProductColor;
use App\traits\ResponseJsonTrait;
class ProductColorController extends Controller
{
   use ResponseJsonTrait;
    public function store(ProductColorRequest $request)
    {
        $product_color = ProductColor::create($request->validated());
        return $this->sendSuccess('Color Added To Product Successfully', $product_color, 201);
    }
    public function destroy($id)
    {
        $product_color = ProductColor::findOrFail($id);
        $product_color->delete();
        return $this->sendSuccess('Color Removed From Product Successfully');
    }
}