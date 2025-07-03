<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
class CartResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'visitor_token' => $this->visitor_token,
            'total_quantity' => $this->total_quantity,
            'total_price' => $this->total_price,
            'items' => CartItemResource::collection($this->items),
        ];
    }
}