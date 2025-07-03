<?php
namespace App\Http\Controllers\API;
use App\Models\ProductColorImage;
use App\traits\ResponseJsonTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Http\Requests\ProductColorImageRequest;

class ProductColorImageController extends Controller
{
    use ResponseJsonTrait;
    public function __construct()
    {
        $this->middleware('auth:admins')->only(['store', 'destroy']);
    }
    public function store(ProductColorImageRequest $request)
    {
        $data = $request->validated();
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePath = $this->uploadImage($image);
                $productColorImage = ProductColorImage::create([
                    'product_color_id' => $data['product_color_id'],
                    'image' => $imagePath,
                ]);
                $images[] = $productColorImage->fresh();
            }
        }
        return $this->sendSuccess('Images Added To Color Of Product Successfully', $images, 201);
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
        $category = ProductColorImage::findOrFail($id);
        if ($category->image && !str_contains($category->image, 'default.jpg')) {
            $imageName = basename($category->image);
            $imagePath = public_path("uploads/products/" . $imageName);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }
        $category->delete();
        return $this->sendSuccess('Image Of Product Color Deleted Successfully!');
    }
}