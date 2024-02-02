<?php

use App\Http\Controllers\Api\AffiliateController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EarnController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SettingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
});

Route::middleware(['api', 'auth:api'])->group(function () {
    /**
     * ==============================
     *            Wallet
     * ==============================
     */
    Route::post('deposit', [WalletController::class, 'deposit']);
    Route::post('withdrawal', [WalletController::class, 'withdrawal']);
    Route::post('buy_coin', [WalletController::class, 'buy_coin']);
    Route::post('internal_transfer', [WalletController::class, 'internal_transfer']);
    Route::post('swap_coin', [WalletController::class, 'swap_coin']);


    Route::get('wallet_history', [WalletController::class, 'wallet_history']);
    Route::get('asset_history', [WalletController::class, 'asset_history']);
    Route::get('setting_wallet_address', [WalletController::class, 'setting_wallet_address']);
    Route::get('setting_coin', [WalletController::class, 'setting_coin']);
    Route::get('user_coins', [WalletController::class, 'user_coins']);
    Route::get('coinMarket', [WalletController::class, 'coinMarket']);
    Route::get('getCoinChart', [WalletController::class, 'getCoinChart']);


    Route::get('notifications', [WalletController::class, 'notifications']);
    Route::post('read_notification', [WalletController::class, 'read_notification']);

    /**
     * ==============================
     *          Affiliate
     * ==============================
     */
    Route::get('my_group', [AffiliateController::class, 'my_group']);

    /**
     * ==============================
     *            Earn
     * ==============================
     */
    Route::get('investment_plans', [EarnController::class, 'investment_plans']);
    Route::get('my_investments', [EarnController::class, 'my_investments']);
    Route::get('earning_history', [EarnController::class, 'earning_history']);
    Route::get('subscription_history', [EarnController::class, 'subscription_history']);
    
    Route::post('subscribe', [EarnController::class, 'subscribe']);

    /**
     * ==============================
     *            Profile
     * ==============================
     */
    Route::get('profile', [ProfileController::class, 'profile']);
    Route::post('update_profile', [ProfileController::class, 'update_profile']);

    /**
     * ==============================
     *            Setting
     * ==============================
     */
    Route::post('change_password', [SettingController::class, 'change_password']);

});
