<?php
namespace App\Http\Requests;
use App\Models\ProductVariant;
use Illuminate\Foundation\Http\FormRequest;

class ProductVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'product_color_id' => 'required|string|exists:product_colors,id',
            'size_id' => 'required|string|exists:sizes,id',
            'quantity' => 'required|integer|min:0',
        ];
    }
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $productColorId = $this->input('product_color_id');
            $sizeId = $this->input('size_id');
            $variantId = $this->route('product_variant') ?? $this->route('id');
            $exists = ProductVariant::where('product_color_id', $productColorId)
                ->where('size_id', $sizeId)
                ->when($variantId, function ($query, $variantId) {
                    return $query->where('id', '!=', $variantId);
                })
                ->exists();
            if ($exists) {
                $validator->errors()->add('product_color_id', 'This color and size combination already exists.');
            }
        });
    }
}