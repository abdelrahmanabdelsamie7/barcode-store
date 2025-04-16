<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class ProductColorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'product_id' => 'required|string|exists:products,id',
            'color_id' => 'required|string|exists:colors,id',
            'is_main' => 'required|boolean',
        ];
    }
}
