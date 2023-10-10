<?php

use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EarnController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WalletController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/upload/tmp_img', [ProfileController::class, 'upload']);
    Route::post('/profile/upload/image-revert', [ProfileController::class, 'image_revert']);

    Route::prefix('wallet')->group(function () {
        Route::get('/details', [WalletController::class, 'details'])->name('wallet.details');
        Route::get('/getTransaction/{type}', [WalletController::class, 'getTransaction'])->name('wallet.getTransaction');
        Route::post('/deposit', [PaymentController::class, 'deposit'])->name('wallet.deposit');
        Route::post('/withdrawal', [PaymentController::class, 'withdrawal'])->name('wallet.withdrawal');
    });

    Route::prefix('earn')->group(function () {
        Route::get('/invest_subscription', [EarnController::class, 'invest_subscription'])->name('earn.invest_subscription');
        Route::post('/subscribe', [EarnController::class, 'subscribe'])->name('earn.subscribe');
        Route::get('/my_investment', [EarnController::class, 'investment'])->name('earn.investment');
    });

    Route::prefix('affiliate')->group(function () {
        Route::get('/referral_view', [AffiliateController::class, 'referral_view'])->name('affiliate.referral_view');
        Route::get('/getTreeData', [AffiliateController::class, 'getTreeData'])->name('affiliate.getTreeData');
    });
});

Route::get('/components/buttons', function () {
    return Inertia::render('Components/Buttons');
})->name('components.buttons');

require __DIR__ . '/auth.php';
