<?php
namespace App\Http\Requests;
use App\Models\{ProductColor,ProductVariant,Size};
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
            $productColor = ProductColor::find($productColorId);
            if (!$productColor) {
                $validator->errors()->add('product_color_id', 'Invalid product color.');
                return;
            }

            $product = $productColor->product;
            if (!$product) {
                $validator->errors()->add('product_color_id', 'Product not found.');
                return;
            }
            $subCategory = $product->sub_category;
            if (!$subCategory) {
                $validator->errors()->add('product_color_id', 'Sub category not found.');
                return;
            }
            $allowedSizesMap = [
                'clothes' => ['S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL', '6XL', '7XL', '8XL', '9XL', '10XL'],
                'pants' => ['29', '30', '32', '34', '36', '38', '40', '42', '44', '46', '48'],
                'shoes' => ['41', '42', '43', '44', '45'],
            ];
            $sizeType = $subCategory->size_type ?? 'clothes';
            $allowedSizes = $allowedSizesMap[$sizeType] ?? $allowedSizesMap['clothes'];
            $size = Size::find($sizeId);
            if (!$size) {
                $validator->errors()->add('size_id', 'Size not found.');
                return;
            }
            if (!in_array($size->name, $allowedSizes)) {
                $validator->errors()->add('size_id', "The size '{$size->name}' is not allowed for this product category.");
            }
        });
    }
}
