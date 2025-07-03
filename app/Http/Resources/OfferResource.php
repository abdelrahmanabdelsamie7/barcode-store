<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
{
    public function toArray($request): array
    {
        $startAt = $this->start_at ? $this->start_at->format('Y-m-d') : null;
        $endAt = $this->end_at ? $this->end_at->format('Y-m-d') : null;
        $durationDays = null;
        if ($this->start_at && $this->end_at) {
            $durationDays = $this->end_at->diffInDays($this->start_at) + 1;
        }
        $now = now();
        if ($this->start_at && $this->end_at) {
            if ($now->between($this->start_at, $this->end_at)) {
                $status = 'active';
            } elseif ($now->lt($this->start_at)) {
                $status = 'upcoming';
            } else {
                $status = 'expired';
            }
        } else {
            $status = 'unknown';
        }
        return [
            'id' => $this->id,
            'discount' => number_format($this->discount, 0),
            'sub_category' => [
                'id' => $this->subCategory->id,
                'category_id' => $this->subCategory->category_id,
                'name' => $this->subCategory->name,
                'slug' => $this->subCategory->slug,
                'image' => url($this->subCategory->image),
                'is_active' => (bool) $this->subCategory->is_active,
            ],
            'start_at' => $startAt,
            'end_at' => $endAt,
            'duration_days' => $durationDays,
            'status' => $status,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}