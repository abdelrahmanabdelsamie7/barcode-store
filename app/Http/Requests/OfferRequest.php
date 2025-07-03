<?php
namespace App\Http\Requests;
use App\Models\Offer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
class OfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        $isUpdate = $this->method() === 'PUT' || $this->method() === 'PATCH';
        return [
            'discount' => $isUpdate ? 'required|numeric|min:1|max:100' : 'nullable|numeric|min:1|max:100',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'sub_category_id' => 'required|uuid|exists:sub_categories,id',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $subCategoryId = $this->input('sub_category_id');
            $startAt = $this->input('start_at');
            $endAt = $this->input('end_at');
            $offerId = $this->route('offer');
            $currentYear = date('Y');
            if ($startAt) {
                $startYear = date('Y', strtotime($startAt));
                if ($startYear != $currentYear) {
                    $validator->errors()->add('start_at', 'The start date must be within the current year.');
                }
            }
            if ($endAt) {
                $endYear = date('Y', strtotime($endAt));
                if ($endYear != $currentYear) {
                    $validator->errors()->add('end_at', 'The end date must be within the current year.');
                }
            }
            if ($startAt && $endAt && $subCategoryId) {
                $conflictingOffers = Offer::where('sub_category_id', $subCategoryId)
                    ->where(function ($query) use ($startAt, $endAt) {
                        $query->whereBetween('start_at', [$startAt, $endAt])
                            ->orWhereBetween('end_at', [$startAt, $endAt])
                            ->orWhere(function ($query) use ($startAt, $endAt) {
                                $query->where('start_at', '<=', $startAt)
                                    ->where('end_at', '>=', $endAt);
                            });
                    });

                if ($offerId) {
                    $conflictingOffers->where('id', '!=', $offerId);
                }

                if ($conflictingOffers->exists()) {
                    $validator->errors()->add('date_range', 'The selected date range conflicts with an existing offer for this sub-category.');
                }
            }
        });
    }

}