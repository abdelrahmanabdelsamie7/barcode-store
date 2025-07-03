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
            'product_color_id' => 'required|uuid|exists:product_colors,id',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp,HEIC|max:5120',
        ];
    }

}
