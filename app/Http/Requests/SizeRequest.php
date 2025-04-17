<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class SizeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'name' => 'required|string|in:S,M,L,XL,XXL,XXXL,4XL,5XL,6XL',
        ];
    }
}