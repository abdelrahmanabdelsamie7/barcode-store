<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class OfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
{
    return [
        'discount' => 'required|numeric|min:0|max:100',
        'start_at' => 'nullable|date',
        'end_at' => 'nullable|date|after_or_equal:start_at',
        'offerable_type' => 'required|string|in:product,subcategory',
        'offerable_id' => [
            'required',
            'uuid',
            function ($attribute, $value, $fail) {
                if ($this->offerable_type === 'product') {
                    // تحقق إذا كان offerable_id موجود في جدول products
                    if (!\App\Models\Product::where('id', $value)->exists()) {
                        $fail('The selected offerable id is invalid.');
                    }
                } elseif ($this->offerable_type === 'subcategory') {
                    // تحقق إذا كان offerable_id موجود في جدول sub_categories
                    if (!\App\Models\SubCategory::where('id', $value)->exists()) {
                        $fail('The selected offerable id is invalid.');
                    }
                }
            }
        ],
    ];
}

}