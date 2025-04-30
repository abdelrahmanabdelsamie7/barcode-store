<?php
namespace App\Http\Requests;
use App\Models\{Product, SubCategory};
use Illuminate\Foundation\Http\FormRequest;
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
            'offerable_type' => 'required|string|in:product,sub_category',
            'offerable_id' => [
                'required',
                'uuid',
                function ($attribute, $value, $fail) {
                    if ($this->offerable_type === 'product') {
                        if (!Product::where('id', $value)->exists()) {
                            $fail('The selected offerable id is invalid.');
                        }
                    } elseif ($this->offerable_type === 'sub_category') {
                        if (!SubCategory::where('id', $value)->exists()) {
                            $fail('The selected offerable id is invalid.');
                        }
                    }
                }
            ],
        ];
    }
}
