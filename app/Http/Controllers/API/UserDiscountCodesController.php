<?php
namespace App\Http\Controllers\API;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\traits\ResponseJsonTrait;
use App\Http\Controllers\Controller;
use App\Models\{UserDiscountCode, User};

class UserDiscountCodesController extends Controller
{
    use ResponseJsonTrait;
    public function index()
    {
        $codes = UserDiscountCode::with(['user', 'campaign'])->get();
        return $this->sendSuccess('User discount codes retrieved successfully.', $codes);
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'campaign_id' => 'required|exists:discount_campaigns,id',
        ]);
        $code = UserDiscountCode::create([
            'user_id' => $data['user_id'],
            'campaign_id' => $data['campaign_id'],
            'code' => strtoupper(Str::random(10)),
        ]);
        return $this->sendSuccess('Discount code created for user.', $code);
    }
    public function bulkGenerate(Request $request)
    {
        $data = $request->validate([
            'campaign_id' => 'required|exists:discount_campaigns,id',
            'per_user' => 'nullable|integer|min:1|max:10',
        ]);
        $users = User::has('orders')->get();
        $codes = [];
        foreach ($users as $user) {
            for ($i = 0; $i < ($data['per_user'] ?? 1); $i++) {
                $codes[] = UserDiscountCode::create([
                    'user_id' => $user->id,
                    'campaign_id' => $data['campaign_id'],
                    'code' => strtoupper(Str::random(10)),
                ]);
            }
        }
        return $this->sendSuccess('Discount codes generated for users.', $codes);
    }
    public function markAsUsed($code)
    {
        $discountCode = UserDiscountCode::where('code', $code)->firstOrFail();
        if ($discountCode->is_used) {
            return $this->sendError('Code already used.');
        }
        $discountCode->update([
            'is_used' => true,
            'used_at' => now(),
        ]);
        return $this->sendSuccess('Discount code marked as used.', $discountCode);
    }
}
