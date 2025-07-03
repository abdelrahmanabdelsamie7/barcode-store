<?php
namespace App\Http\Controllers;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash, Validator};
class AuthAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admins', ['except' => ['login', 'register']]);
    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if (!$token = auth('admins')->attempt($validator->validated())) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
        return $this->respondWithToken($token);
    }
    public function getAccount()
    {
        return response()->json(auth('admins')->user());
    }
    public function logout()
    {
        auth('admins')->logout();
        return response()->json(['message' => 'Successfully Admin logged out']);
    }
    public function refresh()
    {
        return $this->respondWithToken(auth('admins')->refresh());
    }
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('admins')->factory()->getTTL() * 86400,
        ]);
    }
    public function addAdmin(Request $request)
    {
        $authAdmin = auth('admins')->user();
        if (!$authAdmin->is_super_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|min:8',
            'is_super_admin' => 'sometimes|boolean'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $data = $validator->validated();
        $data['password'] = Hash::make($data['password']);
        $admin = Admin::create($data);
        return response()->json([
            'message' => 'Admin created successfully',
            'admin' => $admin
        ], 201);
    }
    public function deleteAdmin($id)
    {
        $authAdmin = auth('admins')->user();
        if (!$authAdmin->is_super_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $admin = Admin::findOrFail($id);
        if ($authAdmin->id === $admin->id) {
            return response()->json(['error' => 'You cannot delete yourself'], 403);
        }
        $admin->delete();
        return response()->json(['message' => 'Admin deleted successfully']);
    }
    public function allAdmins()
    {
        $authAdmin = auth('admins')->user();
        if (!$authAdmin->is_super_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $admins = Admin::where('is_super_admin', false)->get();
        return response()->json([
            "success"=>true,
            "status"=>200,
            "message"=>"All Admin Retrieved Successfully",
            "admins"=>$admins 
        ]);
    }
    public function updatePassword(Request $request, $id = null)
    {
        $authAdmin = auth('admins')->user();
        $targetAdmin = $id ? Admin::findOrFail($id) : $authAdmin;
        if ($authAdmin->id !== $targetAdmin->id && !$authAdmin->is_super_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $rules = [
            'new_password' => 'required|min:8|confirmed'
        ];
        if ($authAdmin->id === $targetAdmin->id) {
            $rules['current_password'] = 'required|min:8';
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if ($authAdmin->id === $targetAdmin->id && !Hash::check($request->current_password, $authAdmin->password)) {
            return response()->json(['error' => 'Current password is incorrect'], 400);
        }
        $targetAdmin->update([
            'password' => Hash::make($request->new_password)
        ]);
        return response()->json(['message' => 'Password updated successfully']);
    }
}
