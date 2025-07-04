<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => [
                'required',
                Rule::in([
                    'Cairo', 'Alexandria', 'Giza', 'Sohag', 'Asyut', 'Mansoura',
                    'Zagazig', 'Tanta', 'Banha', 'Minya', 'Qena', 'Other',
                ]),
            ],
            'payment_method' => [
                'required',
                Rule::in([
                    'Cash on Delivery', 'Vodfone Cach', 'Insta Pay', 'mylo'
                ]),
            ],
            'code' => 'nullable|string|exists:user_discount_codes,code',
            'shipping_cost' => 'nullable|numeric|min:0',
        ];

        if (in_array($this->payment_method, ['Vodfone Cach', 'Insta Pay'])) {
            $rules['payment_phone'] = ['required', 'string', 'max:20'];
            $rules['payment_proof'] = ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'];
            $rules['payment_reference'] = ['nullable', 'string', 'max:255'];
        } elseif ($this->payment_method === 'mylo') {
            $rules['payment_phone'] = ['required', 'string', 'max:20'];
            $rules['payment_proof'] = ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'];
        }
        return $rules;
    }
    public function messages(): array
    {
        return [
            'first_name.required' => 'الاسم الأول مطلوب',
            'last_name.required' => 'الاسم الأخير مطلوب',
            'phone.required' => 'رقم الهاتف مطلوب',
            'address.required' => 'العنوان مطلوب',
            'city.required' => 'المدينة مطلوبة',
            'payment_method.required' => 'طريقة الدفع مطلوبة',
            'payment_phone.required' => 'رقم الهاتف مطلوب لطريقة الدفع المختارة',
            'payment_proof.required' => 'إثبات الدفع مطلوب لطريقة الدفع المختارة',
            'code.exists' => 'كود الخصم غير صالح أو غير موجود',
        ];
    }
}