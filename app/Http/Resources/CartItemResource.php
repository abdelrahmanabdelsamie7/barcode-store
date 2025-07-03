<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray(Request $request): array
{
    $isLight = $request->query('light') === 'true';

    return [
        'id' => $this->id,
        'cart_id' => $this->cart_id,
        'product_variant_id' => $this->product_variant_id,
        'quantity' => $this->quantity,
        'product_variant' => [
            'id' => $this->productVariant->id,
            'size_id' => $this->productVariant->size_id,
            'product_color_id' => $this->productVariant->product_color_id,
            'quantity' => $this->productVariant->quantity,
            'product' => $isLight
                ? new CartProductLightResource($this->productVariant->product)
                : new ProductResource($this->productVariant->product),
        ]
    ];
}

}
