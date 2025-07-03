<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Traits\ResponseJsonTrait;
class WishlistController extends Controller
{
    use ResponseJsonTrait;
    public function index(Request $request)
    {
        $query = Wishlist::with('product');
        if ($request->user()) {
            $query->where('user_id', $request->user()->id);
        } elseif ($request->visitor_token) {
            $query->where('visitor_token', $request->visitor_token);
        } else {
            return $this->sendError('Unauthorized access', 401);
        }
        return $this->sendSuccess('Wishlist retrieved successfully', $query->get());
    }
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'visitor_token' => 'nullable|string',
        ]);
        $userId = $request->user()?->id;
        $visitorToken = $request->visitor_token;
        $alreadyExists = Wishlist::where('product_id', $request->product_id)
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->when(!$userId && $visitorToken, fn($q) => $q->where('visitor_token', $visitorToken))
            ->exists();
        if ($alreadyExists) {
            return $this->sendError('Product already in wishlist', 409);
        }
        $wishlist = Wishlist::create([
            'id' => Str::uuid(),
            'user_id' => $userId,
            'visitor_token' => $visitorToken,
            'product_id' => $request->product_id,
        ]);
        return $this->sendSuccess('Product added to wishlist', $wishlist, 201);
    }
    public function destroy(Request $request, $id)
    {
        $wishlist = Wishlist::findOrFail($id);
        if ($request->user()?->id === $wishlist->user_id || $request->visitor_token === $wishlist->visitor_token) {
            $wishlist->delete();
            return $this->sendSuccess('Item removed from wishlist');
        }
        return $this->sendError('Unauthorized action', 403);
    }
}
