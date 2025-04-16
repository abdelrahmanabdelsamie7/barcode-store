<?php
namespace App\Http\Controllers\API;
use App\Models\Brand;
use App\traits\ResponseJsonTrait;
use App\Http\Requests\BrandRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
class BrandController extends Controller
{
    use ResponseJsonTrait;
    public function index()
    {
        $brands = Brand::all();
        return $this->sendSuccess('Brands Retrieved Successfully!', $brands);
    }
    public function show(string $id)
    {
        $brand = Brand::with('products')->findOrFail($id);
        return $this->sendSuccess('Specific Brand Retrieved Successfully!', $brand);
    }
    public function store(BrandRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request->file('image'));
        }
        $brand = Brand::create($data);
        return $this->sendSuccess('Brand Added Successfully', $brand, 201);
    }
    public function update(BrandRequest $request, string $id)
    {
        $brand = Brand::findOrFail($id);
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $oldImagePath = public_path('uploads/brands/' . basename($brand->image));
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
            $data['image'] = $this->uploadImage($request->file('image'));
        }
        $brand->update($data);
        return $this->sendSuccess('Brand Updated Successfully', $brand, 200);
    }
    private function uploadImage($image)
    {
        if ($image) {
            $imageName = uniqid() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/brands'), $imageName);
            return asset('uploads/brands/' . $imageName);
        }
        return null;
    }
    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);
        if ($brand->image && !str_contains($brand->image, 'default.jpg')) {
            $imageName = basename($brand->image);
            $imagePath = public_path("uploads/brands/" . $imageName);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }
        $brand->delete();
        return $this->sendSuccess('Brand Deleted Successfully');
    }
}
