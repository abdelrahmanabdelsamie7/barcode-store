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
        $sizeId = $this->route('id') ?? $this->route('size');
        return [
            'name' => 'required|string|in:S,M,L,XL,2XL,3XL,4XL,5XL,6XL,7XL,8XL,9XL,10XL|unique:sizes,name,'.$sizeId,
        ];
    }
}