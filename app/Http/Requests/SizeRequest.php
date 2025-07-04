<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class SizeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        $sizeId = $this->route('id') ?? $this->route('size');
        $allowedSizes = [
            'clothes' => ['S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL', '6XL', '7XL', '8XL', '9XL', '10XL'],
            'pants' => ['29', '30', '32', '34', '36', '38', '40', '42', '44', '46', '48'],
            'shoes' => ['41', '42', '43', '44', '45'],
        ];
        $subCategoryId = $this->input('sub_category_id');
        if (!$subCategoryId) {
            return [
                'sub_category_id' => ['required', 'uuid', 'exists:sub_categories,id'],
                'name' => ['required', 'string'],
            ];
        }
        $subCategory = \App\Models\SubCategory::find($subCategoryId);
        $sizeType = $subCategory?->size_type ?? 'clothes';
        $sizes = $allowedSizes[$sizeType] ?? $allowedSizes['clothes'];
        return [
            'sub_category_id' => ['required', 'uuid', 'exists:sub_categories,id'],
            'name' => ['required', 'string', Rule::in($sizes)],
        ];
    }
}
