<?php
namespace App\Http\Controllers\API;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\traits\ResponseJsonTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\DiscountCampaignRequest;
use App\Models\{DiscountCampaign, UserDiscountCode};

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
            ->latest()
            ->get();
        return $this->sendSuccess('Discount campaigns retrieved successfully.', $discountCampaigns);
    }
    public function store(DiscountCampaignRequest $request)
    {
        $validated = $request->validated();
        $campaign = DiscountCampaign::create($validated);
        if ($campaign->type === 'public') {
            for ($i = 0; $i < 10; $i++) {
                UserDiscountCode::create([
                    'campaign_id' => $campaign->id,
                    'code' => strtoupper(Str::random(10)),
                ]);
            }
        }
        return $this->sendSuccess('Discount campaign created successfully.', $campaign);
    }
    public function show($id)
    {
        $discountCampaign = DiscountCampaign::findOrFail($id);
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