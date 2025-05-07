<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class SubCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        $subCategoryId = $this->route('id') ?? $this->route('sub_category');
        return [
            'name' => 'required|string|max:255|unique:sub_categories,name,' . $subCategoryId,
            'slug' => 'required|string|max:255|unique:sub_categories,slug,' . $subCategoryId,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,avif|max:4048',
            'category_id' => 'required|string|exists:categories,id',
            'is_active' => 'required|boolean',
        ];
    }
}
