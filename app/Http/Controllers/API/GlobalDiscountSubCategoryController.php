<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\GlobalDiscountSubCategory;
class GlobalDiscountSubCategoryController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'global_discount_id' => 'required|exists:global_discounts,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $relation = GlobalDiscountSubCategory::create([
            'global_discount_id' => $request->global_discount_id,
            'sub_category_id' => $request->sub_category_id,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Global discount added to sub-category successfully.',
            'data' => $relation,
        ], 201);
    }
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'global_discount_id' => 'required|exists:global_discounts,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $relation = GlobalDiscountSubCategory::where('global_discount_id', $request->global_discount_id)
            ->where('sub_category_id', $request->sub_category_id)
            ->first();
        if (!$relation) {
            return response()->json([
                'success' => false,
                'message' => 'Relationship not found.',
            ], 404);
        }
        $relation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Global discount removed from sub-category successfully.',
        ], 200);
    }
}