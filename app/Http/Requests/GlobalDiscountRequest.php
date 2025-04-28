<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class GlobalDiscountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $globalDiscountId = $this->route('id') ?? $this->route('global_discount');
        return [
            'name' => 'required|string|max:255|unique:global_discounts,name,' . $globalDiscountId,
            'percentage' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive'
        ];
    }
}