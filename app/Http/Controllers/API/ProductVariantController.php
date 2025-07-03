<?php
namespace App\Http\Controllers\API;
use App\Http\Requests\ProductVariantRequest;
use App\Models\ProductVariant;
use App\traits\ResponseJsonTrait;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
class ProductVariantController extends Controller
{
    use ResponseJsonTrait;
    public function __construct()
    {
        $this->middleware('auth:admins')->only(['store', 'update']);
    }
    public function store(ProductVariantRequest $request)
    {
        $product_variant = ProductVariant::create($request->validated());
        return $this->sendSuccess('Add Quantity To Product Size Successfully', $product_variant, 201);
    }
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);
        $product_variant = ProductVariant::findOrFail($id);
        $product_variant->quantity = $validated['quantity'];
        $product_variant->save();
        return $this->sendSuccess('Product Variant Quantity Updated Successfully', $product_variant, 200);
    }
}