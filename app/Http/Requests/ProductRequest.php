<?php
namespace App\Http\Requests;
use App\Enums\MaterialType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        $productId = $this->route('id') ?? $this->route('product');
        return [
            'title' => 'required|string|max:255|unique:products,title,' . $productId,
            'matrial' => ['required', 'string', Rule::in(MaterialType::values())],
            'short_description' => 'required|string',
            'image_cover' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'price_before_discount' => 'required|numeric|min:0|max:99999',
            'status' => 'nullable|in:active,inactive,pending',
            'sub_category_id' => 'required|exists:sub_categories,id',
        ];
    }
}
