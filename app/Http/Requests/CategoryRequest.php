<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        $categoryId = $this->route('id') ?? $this->route('category');
        return [
            'name' => 'required|string|max:255|unique:categories,name,' . $categoryId,
            'slug' => 'required|string|max:255|unique:categories,slug,' . $categoryId,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4048',
            'is_active' => 'required|boolean',
        ];
    }
}
