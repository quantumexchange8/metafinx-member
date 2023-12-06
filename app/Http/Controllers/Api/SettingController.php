<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingController extends Controller
{
    public function change_password(Request $request)
    {
        $user = \Auth::user();
        

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);      

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            "status" => "success",
            "message" => "Password has been successfully changed"
        ]);
    }

}
