<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductColorRequest;
use App\Models\ProductColor;
use App\traits\ResponseJsonTrait;
class ProductColorController extends Controller
{
    use ResponseJsonTrait;
    public function __construct()
    {
        $this->middleware('auth:admins')->only(['store', 'destroy']);
    }
    public function store(ProductColorRequest $request)
    {
        $validated = $request->validated();
        if (!isset($validated['is_main'])) {
            $hasMain = ProductColor::where('product_id', $validated['product_id'])->exists();
            $validated['is_main'] = !$hasMain;
        }
        if ($validated['is_main']) {
            $existingMain = ProductColor::where('product_id', $validated['product_id'])
                ->where('is_main', true)->with('color')->first();
            if ($existingMain) {
                $colorName = $existingMain->color->name ?? 'another color';
                return $this->sendError(
                    "This product already has a main color: {$colorName}. Only one main color is allowed.",
                    422
                );
            }
        }
        $product_color = ProductColor::create($validated);
        return $this->sendSuccess('Color Added To Product Successfully', $product_color, 201);
    }
    public function update(ProductColorRequest $request, $id)
    {
        $color = ProductColor::findOrFail($id);
        $validated = $request->validated();
        if (isset($validated['is_main']) && $validated['is_main']) {
            ProductColor::where('product_id', $color->product_id)
                ->where('id', '!=', $color->id)
                ->update(['is_main' => false]);
        }
        $color->update($validated);
        return $this->sendSuccess('Product Color Updated Successfully', $color);
    }
    public function destroy($id)
    {
        $product_color = ProductColor::findOrFail($id);
        $productId = $product_color->product_id;
        $wasMain = $product_color->is_main;
        $product_color->delete();
        if ($wasMain) {
            $anotherColor = ProductColor::where('product_id', $productId)->first();
            if ($anotherColor) {
                $anotherColor->update(['is_main' => true]);
            }
        }
        return $this->sendSuccess('Color Removed From Product Successfully');
    }
}