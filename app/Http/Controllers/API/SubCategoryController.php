<?php
namespace App\Http\Controllers\API;
use App\Models\SubCategory;
use App\traits\ResponseJsonTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
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
    public function show(string $id)
    {
        $subCategory = SubCategory::with(['products', 'offers'])->findOrFail($id);
        return $this->sendSuccess('Specific Sub Category Retrieved Successfully!', $subCategory);
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
