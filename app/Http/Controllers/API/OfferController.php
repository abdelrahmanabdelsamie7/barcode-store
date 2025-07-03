<?php
namespace App\Http\Controllers\API;
use App\Http\Resources\OfferResource;
use App\Models\Offer;
use App\Http\Requests\OfferRequest;
use App\Http\Controllers\Controller;
use App\traits\ResponseJsonTrait;

class OfferController extends Controller
{
    use ResponseJsonTrait;
    public function __construct()
    {
        $this->middleware('auth:admins')->only(['store', 'update', 'destroy']);
    }
    public function index()
    {
        $offers = Offer::with('subCategory')->get();
        return $this->sendSuccess('All Offers Retrieved Successfully !', OfferResource::collection($offers));
    }
    public function store(OfferRequest $request)
    {
        $offer = Offer::create($request->validated());
        return $this->sendSuccess('Offer Added Successfully', new OfferResource($offer));
    }
    public function show(string $id)
    {
        $offer = Offer::with('subCategory')->findOrFail($id);
        return $this->sendSuccess('Specific Offer Retrieved Successfully !', new OfferResource($offer));
    }
    public function update(OfferRequest $request, string $id)
    {
        $offer = Offer::findOrFail($id);
        $offer->update($request->validated());
        return $this->sendSuccess('Offer Updated Successfully !', new OfferResource($offer), 201);
    }
    public function destroy(string $id)
    {
        $offer = Offer::findOrFail($id);
        $offer->delete();
        return $this->sendSuccess('Offer Deleted Successfully !', null, 204);
    }
}