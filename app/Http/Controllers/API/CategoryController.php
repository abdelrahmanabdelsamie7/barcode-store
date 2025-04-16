<?php
namespace App\Http\Controllers\API;
use App\Models\Category;
use App\traits\ResponseJsonTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Http\Requests\CategoryRequest;

class CategoryController extends Controller
{
    use ResponseJsonTrait;
    public function index()
    {
        $categories = Category::all();
        return $this->sendSuccess('Categories Retrieved Successfully!', $categories);
    }
    public function show(string $id)
    {
        $category = Category::with('sub_categories')->findOrFail($id);
        return $this->sendSuccess('Specific Category Retrieved Successfully!', $category);
    }
    public function store(CategoryRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request->file('image'));
        }
        $category = Category::create($data);
        return $this->sendSuccess('Category Added Successfully', $category, 201);
    }
    public function update(CategoryRequest $request, string $id)
    {
        $category = Category::findOrFail($id);
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $oldImagePath = public_path('uploads/categories/' . basename($category->image));
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
            $data['image'] = $this->uploadImage($request->file('image'));
        }
        $category->update($data);
        return $this->sendSuccess('Category Updated Successfully', $category, 200);
    }
    private function uploadImage($image)
    {
        if ($image) {
            $imageName = uniqid() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/categories'), $imageName);
            return asset('uploads/categories/' . $imageName);
        }
        return null;
    }
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        if ($category->image && !str_contains($category->image, 'default.jpg')) {
            $imageName = basename($category->image);
            $imagePath = public_path("uploads/categories/" . $imageName);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }
        $category->delete();
        return $this->sendSuccess('Category Deleted Successfully');
    }
}
