<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class CartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'cart_id' => 'uuid|exists:carts,id',
            'product_variant_id' => 'required|uuid|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ];
    }
}