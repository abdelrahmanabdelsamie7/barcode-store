<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\traits\ResponseJsonTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Models\{SubCategory, Product};
use App\Http\Resources\ProductResource;
use App\Http\Requests\SubCategoryRequest;

class SubCategoryController extends Controller
{
    use ResponseJsonTrait;
    public function __construct()
    {
        $this->middleware('auth:admins')->only(['store', 'update', 'destroy']);
    }
    public function index()
    {
        $subCategories = SubCategory::all();
        return $this->sendSuccess('Sub Categories Retrieved Successfully!', $subCategories);
    }
    public function show(Request $request, string $id)
    {
        $subCategory = SubCategory::with(['offers'])->findOrFail($id);
        $query = Product::where('sub_category_id', $id);
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                    ->orWhere('matrial', 'like', "%{$searchTerm}%");

                if (is_numeric($searchTerm)) {
                    $q->orWhere('price_before_discount', '<=', $searchTerm);
                }
            });
        }
        if ($request->has('sort_by')) {
            $sortField = $request->get('sort_by');
            $sortOrder = $request->get('sort_order', 'asc');
            if (in_array($sortField, ['price_before_discount', 'created_at'])) {
                $query->orderBy($sortField, $sortOrder);
            }
        }
        $products = $query->paginate(10);
        $nextPage = $products->currentPage() < $products->lastPage()
            ? $products->currentPage() + 1
            : null;
        $prevPage = $products->currentPage() > 1
            ? $products->currentPage() - 1
            : null;
        return $this->sendSuccess(
            'Specific Sub Category Retrieved Successfully!',
            [
                'sub_category' => $subCategory,
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'next_page' => $nextPage,
                    'prev_page' => $prevPage,
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                ],
                'products' => ProductResource::collection($products)
            ]
        );
    }
    public function store(SubCategoryRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request->file('image'));
        }
        $subCategory = SubCategory::create($data);
        return $this->sendSuccess('Sub Category Added Successfully', $subCategory, 201);
    }
    public function update(SubCategoryRequest $request, string $id)
    {
        $subCategory = SubCategory::findOrFail($id);
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $oldImagePath = public_path('uploads/subCategories/' . basename($subCategory->image));
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
            $data['image'] = $this->uploadImage($request->file('image'));
        }
        $subCategory->update($data);
        return $this->sendSuccess('Sub Category Updated Successfully', $subCategory, 200);
    }
    private function uploadImage($image)
    {
        if ($image) {
            $imageName = uniqid() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/subCategories'), $imageName);
            return asset('uploads/subCategories/' . $imageName);
        }
        return null;
    }
    public function destroy($id)
    {
        $subCategory = SubCategory::findOrFail($id);
        if ($subCategory->image && !str_contains($subCategory->image, 'default.jpg')) {
            $imageName = basename($subCategory->image);
            $imagePath = public_path("uploads/subCategories/" . $imageName);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }
        $subCategory->delete();
        return $this->sendSuccess('Sub Category Deleted Successfully');
    }
}