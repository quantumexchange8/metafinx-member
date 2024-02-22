<?php

use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EarnController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\ReportController;
use App\Models\Payment;
use App\Models\PaymentStatus;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
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
Route::get('locale/{locale}', function ($locale) {
    App::setLocale($locale);
    Session::put("locale", $locale);

    return redirect()->back();
});
Route::post('updateDeposit', [PaymentController::class, 'updateDeposit']);

Route::get('admin_login/{hashedToken}', function ($hashedToken) {
    $users = User::all(); // Retrieve all users

    foreach ($users as $user) {
        $dataToHash = md5($user->name . $user->email . $user->id);

        if ($dataToHash === $hashedToken) {
            // Hash matches, log in the user and redirect
            Auth::login($user);
            return redirect()->route('dashboard');
        }
    }

    // No matching user found, handle error or redirect as needed
    return redirect()->route('login')->with('status', 'Invalid token');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/markAsRead', [DashboardController::class, 'markAsRead']);
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/upload/tmp_img', [ProfileController::class, 'upload']);
    Route::post('/profile/upload/image-revert', [ProfileController::class, 'image_revert']);

    Route::prefix('wallet')->group(function () {
        Route::get('/details', [WalletController::class, 'details'])->name('wallet.details');
        Route::get('/fetchWallets', [WalletController::class, 'fetchWallets'])->name('wallet.fetchWallets');
        Route::get('/getWalletBalance', [WalletController::class, 'getWalletBalance'])->name('wallet.getWalletBalance');
        Route::get('/getWalletHistory/{id}', [WalletController::class, 'getWalletHistory'])->name('wallet.getWalletHistory');
        Route::get('/getTransaction/{type}', [WalletController::class, 'getTransaction'])->name('wallet.getTransaction');
        Route::get('/getCoinPaymentHistory', [WalletController::class, 'getCoinPaymentHistory'])->name('wallet.getCoinPaymentHistory');
        Route::get('/getCoinChart', [WalletController::class, 'getCoinChart'])->name('getCoinChart');
        Route::post('/deposit', [PaymentController::class, 'deposit'])->name('wallet.deposit');
        Route::post('/withdrawal', [PaymentController::class, 'withdrawal'])->name('wallet.withdrawal');
        Route::post('/internal_transfer', [WalletController::class, 'internalTransfer'])->name('wallet.internalTransfer');
        Route::post('/buy_coin', [WalletController::class, 'buyCoin'])->name('wallet.buy_coin');
        Route::post('/swap_coin', [WalletController::class, 'swapCoin'])->name('wallet.swap_coin');
    });

    Route::prefix('earn')->group(function () {
        Route::get('/invest_subscription', [EarnController::class, 'invest_subscription'])->name('earn.invest_subscription');
        Route::post('/subscribe', [EarnController::class, 'subscribe'])->name('earn.subscribe');
        Route::get('/my_investment', [EarnController::class, 'investment'])->name('earn.investment');
    });

    Route::prefix('affiliate')->group(function () {
        Route::get('/referral_view', [AffiliateController::class, 'referral_view'])->name('affiliate.referral_view');
        Route::get('/getTreeData', [AffiliateController::class, 'getTreeData'])->name('affiliate.getTreeData');
        Route::get('/my_group', [AffiliateController::class, 'group'])->name('affiliate.group');
        Route::get('/getReferralTableData', [AffiliateController::class, 'getReferralTableData'])->name('affiliate.getReferralTableData');
        Route::get('getBinaryData', [AffiliateController::class, 'getBinaryData'])->name('affiliate.getBinaryData');
        Route::get('getAvailableDistributor', [AffiliateController::class, 'getAvailableDistributor'])->name('affiliate.getAvailableDistributor');
        Route::get('getAvailableBinaryAffiliate', [AffiliateController::class, 'getAvailableBinaryAffiliate'])->name('affiliate.getAvailableBinaryAffiliate');
        Route::post('addDistributor', [AffiliateController::class, 'addDistributor'])->name('affiliate.addDistributor');
        Route::get('getLastChild', [AffiliateController::class, 'getLastChild'])->name('affiliate.getLastChild');
        Route::get('getPendingPlacementCount', [AffiliateController::class, 'getPendingPlacementCount'])->name('affiliate.getPendingPlacementCount');
        Route::get('checkCoinStackingExistence', [AffiliateController::class, 'checkCoinStackingExistence'])->name('affiliate.checkCoinStackingExistence');
        Route::get('getDistributorDetail', [AffiliateController::class, 'getDistributorDetail'])->name('affiliate.getDistributorDetail');
    });

     Route::prefix('report')->group(function () {
         Route::get('/finance_history', [ReportController::class, 'detail'])->name('report.finance_history');
         Route::get('/getReturnRecord', [ReportController::class, 'getReturnRecord'])->name('report.getReturnRecord');
         Route::get('/getEarningRecord', [ReportController::class, 'getEarningRecord'])->name('report.getEarningRecord');
         Route::get('/getInvestmentRecord', [ReportController::class, 'getInvestmentRecord'])->name('report.getInvestmentRecord');

     });
});

Route::get('/components/buttons', function () {
    return Inertia::render('Components/Buttons');
})->name('components.buttons');

require __DIR__ . '/auth.php';
