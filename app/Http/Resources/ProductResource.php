<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        $colors = $this->product_colors->map(function ($colorItem) {
            return [
                'product_color_id' => $colorItem->id,
                'name' => $colorItem->color->name,
                'hex_code' => $colorItem->color->hex_code,
                'is_main' => (bool) $colorItem->is_main,
                'images' => $colorItem->product_color_images->map(function ($img) {
                    return [
                        'id' => $img->id,
                        'image' => $img->image,
                    ];
                }),
                'variants' => $colorItem->product_variants->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'size' => $variant->size->name,
                        'quantity' => $variant->quantity,
                    ];
                }),
            ];
        });
        $colors = $colors->sortByDesc('is_main')->values();
        $mainColor = $colors->firstWhere('is_main', true);
        $coverImage = $mainColor && $mainColor['images']->isNotEmpty()
            ? $mainColor['images'][0]['image']
            : $this->image_cover;
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'matrial' => $this->matrial,
            'short_description' => $this->short_description,
            'image_cover' => $coverImage,
            'price_before_discount' => $this->price_before_discount,
            'final_price' => $this->final_price,
            'status' => $this->status,
            'sub_category' => [
                'id' => $this->sub_category->id,
                'name' => $this->sub_category->name,
                'slug' => $this->sub_category->slug,
                'image' => $this->sub_category->image,
                'offers' => $this->sub_category->offers->map(function ($offer) {
                    return [
                        'id' => $offer->id,
                        'discount' => $offer->discount,
                        'start_at' => $offer->start_at,
                        'end_at' => $offer->end_at,
                    ];
                }),
            ],
            'colors' => $colors,
        ];
    }
}