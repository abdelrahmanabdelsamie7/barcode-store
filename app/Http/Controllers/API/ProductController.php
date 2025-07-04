<?php
namespace App\Http\Controllers\API;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\traits\ResponseJsonTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Http\Requests\ProductRequest;
use App\Models\{Product, HomeProduct};
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    use ResponseJsonTrait;
    public function __construct()
    {
        $this->middleware('auth:admins')->only(['store', 'update', 'destroy']);
    }
    public function index(Request $request)
    {
        $query = Product::query();
        // $query->whereHas('product_variants');
        if ($request->filled('sub_category_id') && $request->sub_category_id !== 'all') {
            $query->where('sub_category_id', $request->sub_category_id);
        }
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
            'All Products Retrieved Successfully!',
            [
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'next_page' => $nextPage,
                    'prev_page' => $prevPage,
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                ],
                'data' => ProductResource::collection($products)
            ]
        );
    }
    public function show(string $id)
    {
        $product = Product::with([
            'sub_category.offers',
            'product_colors.color',
            'product_colors.product_color_images',
            'product_colors.product_variants.size',
        ])->findOrFail($id);
        return response()->json([
            'success' => true,
            'message' => 'Product Details Retrieved Successfully.',
            'data' => new ProductResource($product),
        ]);
    }
    public function store(ProductRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('image_cover')) {
            $data['image_cover'] = $this->uploadImage($request->file('image_cover'));
        }
        $product = Product::create($data);
        return $this->sendSuccess('Product Added Successfully', $product, 201);
    }
    public function update(ProductRequest $request, string $id)
    {
        $product = Product::findOrFail($id);
        $data = $request->validated();
        if ($request->hasFile('image_cover')) {
            $oldImagePath = public_path('uploads/products/' . basename($product->image_cover));
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
            $data['image_cover'] = $this->uploadImage($request->file('image_cover'));
        }
        $product->update($data);
        return $this->sendSuccess('Product Data Updated Successfully', $product, 200);
    }
    private function uploadImage($image)
    {
        if ($image) {
            $imageName = uniqid() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/products'), $imageName);
            return asset('uploads/products/' . $imageName);
        }
        return null;
    }
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        if ($product->image_cover && !str_contains($product->image_cover, 'default.jpg')) {
            $imageName = basename($product->image_cover);
            $imagePath = public_path("uploads/products/" . $imageName);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }
        $product->delete();
        return $this->sendSuccess('Product Deleted Successfully');
    }
    public function homeProducts()
    {
        $today = Carbon::today();
        $homeProducts = HomeProduct::with('product')->where('date', $today)->get();
        if ($homeProducts->count() < 10) {
            $usedIds = $homeProducts->pluck('product_id')->toArray();
            $remainingCount = 10 - $homeProducts->count();
            $remaining = Product::where('status', 'active')
                ->whereNotIn('id', $usedIds)
                ->inRandomOrder()
                ->limit($remainingCount)
                ->get();
            foreach ($remaining as $product) {
                HomeProduct::create([
                    'product_id' => $product->id,
                    'date' => $today,
                ]);
            }
            if ($homeProducts->count() + $remaining->count() < 10) {
                $homeProducts = HomeProduct::with('product')
                    ->where('date', $today)
                    ->get();
            } else {
                $homeProducts = HomeProduct::with('product')->where('date', $today)->get();
            }
        }
        return $this->sendSuccess(
            'Home Section Products Retrieved Successfully!',
            ProductResource::collection($homeProducts->pluck('product'))
        );
    }
}