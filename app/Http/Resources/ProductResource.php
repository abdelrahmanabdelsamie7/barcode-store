<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'cover_image' => $this->image_cover,
            'sku' => $this->sku,
            'price_before_discount' => $this->price_before_discount,
            'final_price' => $this->price_before_discount * (1 - ($this->discount ?? 0) / 100), // استخدام الخصم من الـ Accessor
            'status' => $this->status,
            'offers' => $this->offers, // جلب العروض المرتبطة
            'sub_category' => [
                'id' => $this->sub_category->id,
                'name' => $this->sub_category->name,
                'slug' => $this->sub_category->slug,
                'image' => $this->sub_category->image,
            ],
            'brand' => [
                'id' => $this->brand->id,
                'name' => $this->brand->name,
                'slug' => $this->brand->slug,
                'image' => $this->brand->image,
            ],
            'colors' => $this->product_colors->map(function ($colorItem) {
                return [
                    'name' => $colorItem->color->name,
                    'code' => $colorItem->color->color_code,
                    'is_main' => (bool) $colorItem->is_main,
                    'images' => $colorItem->product_color_images->pluck('image'),
                    'variants' => $colorItem->product_variants->map(function ($variant) {
                        return [
                            'size' => $variant->size->name,
                            'quantity' => $variant->quantity,
                        ];
                    }),
                ];
            }),
        ];
    }
}
