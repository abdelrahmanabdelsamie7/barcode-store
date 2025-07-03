<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\CartService;
use Illuminate\Support\Facades\{Hash, Validator};
class AuthUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['register', 'login', 'getAccount', 'verifyPhone']]);
    }
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'whatsapp_phone' => [
                'required',
                'string',
                'max:15',
                'unique:users,whatsapp_phone',
                'regex:/^01[0125][0-9]{8}$/',
            ],
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $normalizedPhone = '+20' . ltrim($request->whatsapp_phone, '0');
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'whatsapp_phone' => $normalizedPhone,
            'password' => Hash::make($request->password),
            'phone_verification_code' => null,
            'phone_verified_at' => now(),
        ]);
        $token = auth('api')->login($user);
        if (!$token) {
            return response()->json(['error' => 'Failed to create token'], 500);
        }
        $visitorToken = $request->cookie('visitor_token') ?? $request->header('X-Visitor-Token');
        app(CartService::class)->transferVisitorCartToUser($visitorToken, $user->id);
        return response()->json([
            'message' => 'Registration successful.',
            'token' => $token,
            'user' => $user,
        ], 201);
    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'whatsapp_phone' => [
                'required',
                'string',
                'max:15',
                'regex:/^01[0125][0-9]{8}$/'
            ],
            'password' => 'required|string|min:8',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $normalizedPhone = '+20' . ltrim($request->whatsapp_phone, '0');
        $user = User::where('whatsapp_phone', $normalizedPhone)->first();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Password mismatch'], 401);
        }
        $token = auth('api')->login($user);
        if (!$token) {
            return response()->json(['error' => 'Failed to create token'], 500);
        }
        $visitorToken = $request->cookie('visitor_token') ?? $request->header('X-Visitor-Token');
        app(CartService::class)->transferVisitorCartToUser($visitorToken, $user->id);
        return response()->json([
            'message' => 'Login successful.',
            'token' => $token,
            'user' => $user,
        ]);
    }
    public function logout()
    {
        auth('api')->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }
    public function getAccount(Request $request)
    {
        $user = auth('api')->user()->load(['discountCodes:id,user_id,code,is_used']);
        return response()->json([
            "message"=>"User Account Retrieved Successfully",
            "success" => true,
            "status" => 200,
            "data" => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'whatsapp_phone' => $user->whatsapp_phone,
                'discount_codes' => $user->discountCodes,
            ]
        ]);

    }
    // public function verifyPhone(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'whatsapp_phone' => 'required|string|exists:users,whatsapp_phone',
    //         'code' => 'required|numeric|digits:4',
    //     ]);
    //     if ($validator->fails()) {
    //         return response()->json($validator->errors(), 400);
    //     }
    //     $cleanPhone = preg_replace('/[^0-9]/', '', $request->whatsapp_phone);
    //     $fullPhone = '+' . $cleanPhone;
    //     $user = User::where('whatsapp_phone', $fullPhone)->first();
    //     if (!$user || $user->phone_verification_code !== $request->code) {
    //         return response()->json(['error' => 'Invalid verification code.'], 400);
    //     }
    //     $user->update([
    //         'phone_verified_at' => now(),
    //         'phone_verification_code' => null,
    //     ]);
    //     return response()->json(['message' => 'Phone verified successfully.']);
    // }
}