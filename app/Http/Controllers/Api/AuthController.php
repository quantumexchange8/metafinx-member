<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\InvestmentSubscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $credentials = $request->only('email', 'password');

        $token = auth()->guard('api')->attempt($credentials);

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = auth()->guard('api')->user();
        $user_affiliate_ids = $user->getChildrenIds();
        $valid_self_deposit = InvestmentSubscription::query()
            ->where('user_id', $user->id)
            ->whereDate('expired_date', '>', now())
            ->sum('amount');

        $valid_affiliate_deposit = InvestmentSubscription::query()
            ->whereIn('user_id', $user_affiliate_ids)
            ->whereDate('expired_date', '>', now())
            ->sum('amount');

        $user_data = [
            'name' => $user->name,
            'image_address' => $user->getFirstMediaUrl('profile_photo'),
            'rank' => $user->setting_rank_id,
            'verified' => $user->kyc_approval,
            'phone' => $user->phone,
            'email' => $user->email,
            'nationality' => $user->country,
            'identity_number' => $user->identity_number,
            'address_line_1' => $user->address_1,
            'address_line_2' => $user->address_2,
            'wallets' => $user->wallets,
            'affiliate' => count($user_affiliate_ids),
            'vsd' => $valid_self_deposit,
            'vad' => $valid_affiliate_deposit,
        ];

        return response()->json([
            'status' => 'success',
            'user' => $user_data,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);

    }

    public function register(Request $request){

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = Auth::login($user);
        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function logout()
    {
        auth()->guard('api')->logout();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }
}
