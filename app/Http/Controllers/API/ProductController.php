<?php
namespace App\Http\Controllers\API;
use App\Models\Product;
use App\traits\ResponseJsonTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    use ResponseJsonTrait;
    public function index()
    {
        $products = Product::all();
        return $this->sendSuccess('All Products Retrieved Successfully!', $products);
    }
    public function show(string $id)
    {
        $product = Product::with(['sub_category.globalDiscounts', 'product_colors.product_color_images', 'product_colors.product_variants.size', 'product_colors.color', 'brand', 'globalDiscounts'])->findOrFail($id);
        return $this->sendSuccess('Product details fetched successfully.', new ProductResource($product));
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
}