<?php
namespace App\Http\Controllers\API;
use App\Models\{Offer, Product, SubCategory};
use App\traits\ResponseJsonTrait;
use App\Http\Requests\OfferRequest;
use App\Http\Controllers\Controller;

class OfferController extends Controller
{
    use ResponseJsonTrait;
    public function index()
    {
        $offers = Offer::all();
        return $this->sendSuccess('All Offers Retrieved Successfully!', $offers);
    }
    public function show(string $id)
    {
        $offer = Offer::with('offerable')->findOrFail($id);
        return $this->sendSuccess('Specific Offer Retrieved Successfully!', $offer);
    }
    public function store(OfferRequest $request)
    {
        $offerableType = $request->offerable_type;
        $offerableId = $request->offerable_id;
        if (!in_array($offerableType, ['product', 'sub_category'])) {
            return response()->json(['message' => 'Invalid offerable type'], 422);
        }
        $offerableModel = $offerableType === 'product' ? Product::class : SubCategory::class;
        $offerable = $offerableModel::find($offerableId);
        if (!$offerable) {
            return response()->json(['message' => 'Offerable not found'], 404);
        }
        $offer = Offer::create([
            'discount' => $request->discount,
            'start_at' => $request->start_at,
            'end_at' => $request->end_at,
            'offerable_type' => $offerableType,
            'offerable_id' => $offerableId
        ]);
        return response()->json([
            'message' => 'Offer created successfully!',
            'offer' => $offer
        ]);
    }
    public function update(OfferRequest $request, string $id)
    {
        $offer = Offer::findOrFail($id);
        $offer->update($request->validated());
        return $this->sendSuccess('Offer Updated Successfully', $offer, 200);
    }
    public function destroy($id)
    {
        $offer = Offer::findOrFail($id);
        $offer->delete();
        return $this->sendSuccess('Offer Deleted Successfully');
    }
}
