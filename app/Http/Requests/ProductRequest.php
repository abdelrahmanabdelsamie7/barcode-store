<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'slug' => 'required|string|max:255|unique:products,slug,' . $productId,
            'description' => 'required|string',
            'image_cover' => 'required|image|mimes:jpeg,png,jpg,gif|max:4048',
            'sku' => 'nullable|string|max:255|unique:products,sku,' . $productId,
            'price_before_discount' => 'required|numeric|min:0',
            'discount' => 'nullable|integer|min:0|max:100',
            'price_after_discount' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,pending',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'brand_id' => 'required|exists:brands,id',
        ];
    }
}