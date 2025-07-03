<?php
namespace App\Http\Controllers\API;
use App\Models\Category;
use App\traits\ResponseJsonTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;

class CategoryController extends Controller
{
    use ResponseJsonTrait;
    public function __construct()
    {
        $this->middleware('auth:admins')->only(['store', 'update', 'destroy']);
    }
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
        $category = Category::create($data);
        return $this->sendSuccess('Category Added Successfully', $category, 201);
    }
    public function update(CategoryRequest $request, string $id)
    {
        $category = Category::findOrFail($id);
        $data = $request->validated();
        $category->update($data);
        return $this->sendSuccess('Category Updated Successfully', $category, 200);
    }
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return $this->sendSuccess('Category Deleted Successfully');
    }
}