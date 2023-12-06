<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\InvestmentSubscription;
use App\Models\User;
use App\Models\SettingCountry;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules;


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
        if (!$user->email_verified_at) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email is not verified.',
            ], 401);
        }

        $user_data = [
            'name' => $user->name,
            'email_verified' => $user->email_verified_at,
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

    public function register(Request $request)
    {

        $settingCountries = SettingCountry::all();

        $formattedCountries = $settingCountries->map(function ($country) {
            return [
                'value' => $country->name_en,
                'label' => $country->name_en,
                'phone_code' => $country->phone_code,
            ];
        });

        $validCountries = $formattedCountries->pluck('value')->toArray();

        $request->validate([
            'name' => 'required|regex:/^[a-zA-Z0-9\p{Han}. ]+$/u|max:255',
            'country' => 'required|in:' . implode(',', $validCountries),
            'email' => 'required|string|email|max:255|unique:' . User::class,
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:8|unique:' . User::class,
            'address_1' => 'required',
            'address_2' => 'nullable',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'verification_type' => 'required|in:nric,passport',
            'identity_number' => 'required|unique:users,identity_number',
            'proof_front' => 'nullable',
            'proof_back' => 'nullable',
            'referral_code' => 'nullable',
            'terms' => 'accepted',
        ]);

        if($request->has('referral_code'))
        {
            $referral_code = $request->input('referral_code');

            $check_referral_code = User::where('referral_code', $referral_code)->first();

            if($check_referral_code)
            {
                $upline_id = $check_referral_code->id;

                // if($check_referral_code->hierarchyList != null)
                // {
                //     $upline_hierarchy = $check_referral_code->hierarchyList;
                // }

                if(empty($check_referral_code['hierarchyList'])){
                    $hierarchyList = "-" . $upline_id . "-";
                } else {
                    $hierarchyList = $check_referral_code['hierarchyList'] . $upline_id . "-";
                }

                $user = User::create([
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'country' => $request->country,
                    'address_1' => $request->address_1,
                    'address_2' => $request->address_2,
                    'password' => Hash::make($request->password),
                    'verification_type' => $request->verification_type,
                    'identity_number' => $request->identity_number,
                    'upline_id' => $upline_id,
                    'hierarchyList' => $hierarchyList,
                    'setting_rank_id' => 1,
                    'role' => 'user',
                    'kyc_approval' => 'unverified',
                ]);
            }
        } else {
            $user = User::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'country' => $request->country,
                'address_1' => $request->address_1,
                'address_2' => $request->address_2,
                'password' => Hash::make($request->password),
                'verification_type' => $request->verification_type,
                'identity_number' => $request->identity_number,
                'setting_rank_id' => 1,
                'role' => 'user',
                'kyc_approval' => 'unverified',
            ]);
        }

        if ($request->hasFile('proof_front')) {
            $file = $request->file('proof_front');

            $originalFilename = $file->getClientOriginalName();
            $proof_front = $file->storeAs('uploads/user/kyc', $originalFilename, 'public');
            $path = storage_path('/app/public/' . $proof_front);
            if (file_exists($path)) {
                $user->clearMediaCollection('front_identity');
                $user->addMedia($path)->toMediaCollection('front_identity');
                $user->update([
                    'kyc_approval' => 'pending'
                ]);
            }
        }

        if ($request->hasFile('proof_back')) {
            $file = $request->file('proof_back');

            $originalFilename = $file->getClientOriginalName();
            $proof_back = $file->storeAs('uploads/user/kyc', $originalFilename, 'public');
            $path = storage_path('/app/public/' . $proof_back);
            if (file_exists($path)) {
                $user->clearMediaCollection('back_identity');
                $user->addMedia($path)->toMediaCollection('back_identity');
                $user->update([
                    'kyc_approval' => 'pending'
                ]);
            }
        }

        Wallet::create([
            'user_id' => $user->id,
            'name' => 'Internal Wallet'
        ]);

        $user->setReferralId();

        event(new Registered($user));

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
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
