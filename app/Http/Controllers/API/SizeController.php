<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Http\Requests\SizeRequest;
use App\Models\Size;
use App\Models\SubCategory;
use App\traits\ResponseJsonTrait;
class SizeController extends Controller
{
    use ResponseJsonTrait;
    public function __construct()
    {
        $this->middleware('auth:admins')->only(['store', 'destroy']);
    }
    public function index()
    {
        $sizes = Size::orderBy('order')->get();
        return $this->sendSuccess('All Avaliable Sizes Retrieved Successfully!', $sizes);
    }
    public function getSizesBySubCategory($subCategoryId)
    {
        $allowedSizes = [
            'clothes' => ['S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL', '6XL', '7XL', '8XL', '9XL', '10XL'],
            'pants' => ['29', '30', '32', '34', '36', '38', '40', '42', '44', '46', '48'],
            'shoes' => ['41', '42', '43', '44', '45'],
        ];
        $subCategory = SubCategory::find($subCategoryId);
        $sizeType = $subCategory?->size_type ?? 'clothes';
        $sizesNames = $allowedSizes[$sizeType] ?? $allowedSizes['clothes'];
        $sizes = Size::whereIn('name', $sizesNames)->get(['id', 'name']);
        return response()->json([
            'success' => true,
            'data' => $sizes
        ]);
    }
    public function store(SizeRequest $request)
    {
        $size = Size::create([
            'name' => $request->name,
            'order' => $this->getOrderForSize($request->name),
        ]);
        return $this->sendSuccess('New Size Added Successfully', $size, 201);
    }
    public function destroy($id)
    {
        $size = Size::findOrFail($id);
        $size->delete();
        return $this->sendSuccess('Size Deleted Successfully');
    }
    private function getOrderForSize($size)
    {
        $sizes = ['S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL', '6XL', '7XL', '8XL', '9XL', '10XL'];
        return array_search($size, $sizes);
    }
}