<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Http\Requests\GlobalDiscountRequest;
use App\Models\GlobalDiscount;
use App\traits\ResponseJsonTrait;

class GlobalDiscountController extends Controller
{
    use ResponseJsonTrait;
    public function index()
    {
        $globalDiscounts = GlobalDiscount::all();
        return $this->sendSuccess('All Global Discounts are Retrieved Successfully!', $globalDiscounts);
    }
    public function store(GlobalDiscountRequest $request)
    {
        $globalDiscount = GlobalDiscount::create($request->validated());
        return $this->sendSuccess('Global Discount Added Successfully', $globalDiscount, 201);
    }
    public function update(GlobalDiscountRequest $request, string $id)
    {
        $globalDiscount = GlobalDiscount::findOrFail($id);
        $globalDiscount->update($request->validated());
        return $this->sendSuccess('Global Discount Updated Successfully', $globalDiscount, 200);
    }
    public function destroy($id)
    {
        $globalDiscount = GlobalDiscount::findOrFail($id);
        $globalDiscount->delete();
        return $this->sendSuccess('Global Discount Deleted Successfully');
    }
}