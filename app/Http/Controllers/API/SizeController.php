<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Http\Requests\SizeRequest;
use App\Models\Size;
use App\traits\ResponseJsonTrait;
class SizeController extends Controller
{
    use ResponseJsonTrait;
    public function index()
    {
        $sizes = Size::all();
        return $this->sendSuccess('All Avaliable Sizes Retrieved Successfully!', $sizes);
    }
    public function store(SizeRequest $request)
    {
        $size = Size::create($request->validated());
        return $this->sendSuccess('New Size Added Successfully', $size, 201);
    }
    public function destroy($id)
    {
        $size = Size::findOrFail($id);
        $size->delete();
        return $this->sendSuccess('Size Deleted Successfully');
    }
}