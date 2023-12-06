<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\InvestmentSubscription;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function profile()
    {
        $user = \Auth::user();
        $user_affiliate_ids = $user->getChildrenIds();
        $valid_self_deposit = InvestmentSubscription::query()
            ->where('user_id', $user->id)
            ->whereDate('expired_date', '>', now())
            ->sum('amount');

        $valid_affiliate_deposit = InvestmentSubscription::query()
            ->whereIn('user_id', $user_affiliate_ids)
            ->whereDate('expired_date', '>', now())
            ->sum('amount');

        $profile = [
            'name' => $user->name,
            'image_address' => $user->getFirstMediaUrl('profile_photo'),
            'rank' => $user->setting_rank_id,
            'verified' => $user->kyc_approval,
            'phone' => $user->phone,
            'email' => $user->email,
            'nationality' => $user->country,
            'nric' => $user->identity_number,
            'address_line_1' => $user->address_1,
            'address_line_2' => $user->address_2,
            'affiliate' => count($user_affiliate_ids),
            'vsd' => $valid_self_deposit,
            'vad' => $valid_affiliate_deposit,
            'wallets' => $user->wallets,
        ];

        return response()->json([
            'profile' => $profile,
        ]);
    }

    public function update_profile(Request $request)
    {
        $user = \Auth::user();

        $profile = [
            'name' => $user->name,
            'verified' => $user->kyc_approval,
            'profile_image' => $user->getFirstMediaUrl('profile_photo'),
            'front_image' => $user->getFirstMediaUrl('front_identity'),
            'back_image' => $user->getFirstMediaUrl('back_identity'),
        ];

        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');

            $originalFilename = $file->getClientOriginalName();
            $profile_photo = $file->storeAs('uploads/user/profile_photo', $originalFilename, 'public');
            $path = storage_path('/app/public/' . $profile_photo);
            if (file_exists($path)) {
                $user->clearMediaCollection('profile_photo');
                $user->addMedia($path)->toMediaCollection('profile_photo');
            }
        }

        if ($user->kyc_approval != 'verified') {
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
        }

        $new_profile = [
            'name' => $user->name,
            'verified' => $user->kyc_approval,
            'profile_image' => $user->getFirstMediaUrl('profile_photo'),
            'front_image' => $user->getFirstMediaUrl('front_identity'),
            'back_image' => $user->getFirstMediaUrl('back_identity'),
        ];

        return response()->json([
            'profile' => $profile,
            'new_profile' => $new_profile,
        ]);
    }

}
