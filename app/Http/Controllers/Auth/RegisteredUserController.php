<?php

namespace App\Http\Controllers\Auth;

use App\Models\Coin;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Wallet;
use App\Models\CoinStacking;
use Illuminate\Http\Request;
use App\Models\CoinMultiLevel;
use App\Models\SettingCountry;
use Illuminate\Validation\Rules;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\RegisterRequest;
use Illuminate\Auth\Events\Registered;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Validator;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create($referral = null)
    {
        $position = request()->query('position'); // Retrieve 'position' from query parameters
    
        $settingCountries = SettingCountry::all();
        
        $formattedCountries = $settingCountries->map(function ($country) {
            return [
                'value' => $country->name_en,
                'label' => $country->name_en,
                'phone_code' => $country->phone_code,
            ];
        });
    
        return Inertia::render('Auth/Register', [
            'countries' => $formattedCountries,
            'referral' => $referral,
            'position' => $position,
        ]);
    }
        
    public function firstStep(Request $request)
    {
        $rules = [
            'name' => 'required|regex:/^[a-zA-Z0-9\p{Han}. ]+$/u|max:255',
            'country' => 'required',
            'email' => 'required|string|email|max:255|unique:' . User::class,
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:8|unique:' . User::class,
            'address_1' => 'required',
        ];

        $attributes = [
            'name'=> trans('public.profile.name'),
            'country' => trans('public.profile.country'),
            'email' => trans('public.profile.email'),
            'phone' => trans('public.profile.phone_number'),
            'address_1' => trans('public.profile.address'),
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributes);

        if ($request->form_step == 1) {
            $validator->validate();
        } elseif ($request->form_step == 2) {
            $additionalRules = [
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ];
            $rules = array_merge($rules, $additionalRules);

            $additionalAttributes = [
                'password' => trans('public.profile.password'),
            ];
            $attributes = array_merge($attributes, $additionalAttributes);

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attributes);
            $validator->validate();
        } elseif ($request->form_step == 3) {

            $additionalRules = [
                'verification_type' => 'required',
                'identity_number' => 'required|unique:users,identity_number',
                'proof_front' => 'nullable',
                'proof_back' => 'nullable',
            ];
            $rules = array_merge($rules, $additionalRules);

            $additionalAttributes = [
                'verification_type' => 'Verification Type',
                'identity_number' => trans('public.profile.identification_number'),
                'passport_number' => 'Passport Number',
                'proof_front' => 'Proof of Identity (FRONT)',
                'proof_back' => 'Proof of Identity (BACK)',
            ];
            $attributes = array_merge($attributes, $additionalAttributes);

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attributes);
            $validator->validate();
        }

        return to_route('register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
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
                    'email' => $request->email,
                    'country' => $request->country,
                    'phone' => $request->phone,
                    'address_1' => $request->address_1,
                    'address_2' => $request->address_2,
                    'verification_type' => $request->verification_type,
                    'upline_id' => $upline_id,
                    'hierarchyList' => $hierarchyList,
                    'setting_rank_id' => 1,
                    'password' => Hash::make($request->password),
                    'identity_number' => $request->identity_number,
                    'role' => 'user',
                    'kyc_approval' => 'unverified',
                ]);
            }
        } else {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'country' => $request->country,
                'phone' => $request->phone,
                'address_1' => $request->address_1,
                'address_2' => $request->address_2,
                'verification_type' => $request->verification_type,
                'setting_rank_id' => 1,
                'password' => Hash::make($request->password),
                'identity_number' => $request->identity_number,
                'role' => 'user',
                'kyc_approval' => 'unverified',
            ]);
        }

        if ($user->created_at->lt(now()->setTime(17, 0, 0))) {
            // If created_at is before today at 5 PM
            $autoAssignDate = now()->addDay()->startOfDay();
        } else {
            // If created_at is at or after today at 5 PM
            $autoAssignDate = now()->addDays(2)->startOfDay();
        }

        $user->update([
            'auto_assign_at' => $autoAssignDate
        ]);

        Wallet::create([
            'user_id' => $user->id,
            'name' => 'Internal Wallet',
            'type' => 'internal_wallet',
        ]);

        Wallet::create([
            'user_id' => $user->id,
            'name' => 'MUSD Wallet',
            'type' => 'musd_wallet',
        ]);

        $coin = Coin::create([
            'user_id' => $user->id,
            'setting_coin_id' => 1,
        ]);

        $coin->setCoinAddress();

        $user->setReferralId();

        $this->processImage($request, $user);

        event(new Registered($user));

        // Call addDistributor method and pass the newly created user's ID
        $this->addDistributor($user->id, $request->position, $request->referral_code);

        return redirect()->route('login')->with('title', trans('public.account_sign_up'))->with('success', trans('public.account_sign_up_message'));
    }

    public function upload(Request $request)
    {
        if ($request->hasFile('proof_front')) {
            $file = $request->file('proof_front');

            $originalFilename = $file->getClientOriginalName();
            return $file->storeAs('uploads/user/kyc', $originalFilename, 'public');
        }

        if ($request->hasFile('proof_back')) {
            $file = $request->file('proof_back');

            $originalFilename = $file->getClientOriginalName();
            return $file->storeAs('uploads/user/kyc', $originalFilename, 'public');
        }

        return '';
    }

    public function image_revert(Request $request)
    {
        if ($image = $request->get('image')) {
            $path = storage_path('/app/public/' . $image);
            if (file_exists($path)) {
                unlink($path);
            }
        }

        if ($image = $request->get('image_back')) {
            $path = storage_path('/app/public/' . $image);
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }

    protected function processImage(Request $request, $user): void
    {
        if ($image = $request->get('proof_front')) {
            $path = storage_path('/app/public/' . $image);
            if (file_exists($path)) {
                $user->addMedia($path)->toMediaCollection('front_identity');
                $user->update([
                    'kyc_approval' => 'pending'
                ]);
            }
        }

        if ($image_back = $request->get('proof_back')) {
            $path = storage_path('/app/public/' . $image_back);
            if (file_exists($path)) {
                $user->addMedia($path)->toMediaCollection('back_identity');
                $user->update([
                    'kyc_approval' => 'pending'
                ]);
            }
        }
    }

    public function addDistributor($userId, $position, $referral_code)
    {
        $lastChild = $this->getLastChild($position,$referral_code);
        $upline = CoinMultiLevel::find($lastChild->id);
        $sponsor_user_id = User::where('referral_code', $referral_code)->first();
        $sponsor = CoinMultiLevel::where('user_id', $sponsor_user_id->id)->first();
        $coinStakingPrice = CoinStacking::where('user_id', $userId)->where('status', 'OnGoingPeriod')->sum('stacking_price');
    
        // Check if the specified position is available in the upline's direct child
        $directChild = $upline->direct_child($position)->first();
        
        // Update the hierarchy list based on the upline
        if ($upline->id == 1) {
            // If the upline is the root node, the hierarchy list will be the user's ID
            $hierarchyList = '-' . $lastChild->id . '-';
        } else {
            // Otherwise, prepend the upline's hierarchy list with a '-' if it's not empty
            $hierarchyList = $upline->hierarchy_list . $upline->id . '-';
        }

        // Prepare data for creating the distributor
        $data = [
            'user_id' => $userId,
            'sponsor_id' => $sponsor->id,
            'upline_id' => $upline->id,
            'hierarchy_list' => $hierarchyList,
            'position' => 'left', // default position
            'coin_stacking_amount' => $coinStakingPrice,
        ];
    
        // Check if the position can be updated
        if (empty($upline->direct_child('left')->first()) && empty($upline->direct_child('right')->first())) {
            if ($position == 'right' && $upline->id == $sponsor->id) {
                $data['position'] = $position;
            }
        } elseif ($position == 'right' && empty($directChild)) {
            $data['position'] = $position;
        }
    
        // Create the distributor with the provided parameters
        CoinMultiLevel::create($data);
    }

    public function getLastChild($position, $referral_code)
    {
        $sponsor_user_id = User::where('referral_code', $referral_code)->first();
        $binaryAuthUser = CoinMultiLevel::where('user_id', $sponsor_user_id->id)->first();

        $directChild = $binaryAuthUser->direct_child($position)->first();

        $last_child = $directChild ? $directChild->getLastChild($directChild, 'left') : $binaryAuthUser;
        if ($last_child) {
            $last_child->profile_photo = $last_child->user->getFirstMediaUrl('profile_photo');
        }

        return $last_child;
    }

}
