<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class ProductColorImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'product_color_id' => 'required|string|exists:product_colors,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4048',
        ];
    }
}