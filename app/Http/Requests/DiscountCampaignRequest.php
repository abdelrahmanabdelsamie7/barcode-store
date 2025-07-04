<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class DiscountCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'discount_type' => 'required|in:percent,amount',
            'type' => 'required|in:user_only,public',
            'discount_value' => 'required|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
        ];
    }
}