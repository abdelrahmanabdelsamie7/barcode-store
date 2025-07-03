<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Models\DiscountCampaign;
use App\traits\ResponseJsonTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\DiscountCampaignRequest;

class DiscountCampaignController extends Controller
{
    use ResponseJsonTrait;
    public function __construct()
    {
        $this->middleware('auth:admins')->only(['index', 'store', 'show', 'update', 'destroy']);
    }
    public function index(Request $request)
    {
        $discountCampaigns = DiscountCampaign::query()
            ->when($request->has('search'), fn($q) => $q->where('name', 'like', '%' . $request->search . '%'))
            ->when($request->has('type'), fn($q) => $q->where('discount_type', $request->type))
            ->with('subCategory')
            ->latest()
            ->get();
        return $this->sendSuccess('Discount campaigns retrieved successfully.', $discountCampaigns);
    }
    public function store(DiscountCampaignRequest $request)
    {
        $validated = $request->validated();
        if (!$validated || !is_array($validated)) {
            return $this->sendError('Invalid data provided.', 422);
        }
        $campaign = DiscountCampaign::create($validated);
        return $this->sendSuccess('Discount campaign created successfully.', $campaign);
    }
    public function show($id)
    {
        $discountCampaign = DiscountCampaign::with('subCategory')->findOrFail($id);
        return $this->sendSuccess('Discount campaign retrieved successfully.', $discountCampaign);
    }
    public function update(DiscountCampaignRequest $request, $id)
    {
        $campaign = DiscountCampaign::findOrFail($id);
        $campaign->update($request->validated());
        return $this->sendSuccess('Discount campaign updated successfully.', $campaign);
    }
    public function destroy($id)
    {
        $campaign = DiscountCampaign::findOrFail($id);
        $campaign->delete();
        return $this->sendSuccess('Discount campaign deleted successfully.');
    }
}