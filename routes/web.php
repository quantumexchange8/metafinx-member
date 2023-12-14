<?php

use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EarnController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\ReportController;
use App\Models\Payment;
use App\Models\PaymentStatus;
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

Route::post('updateDeposit', function (Request $request) {
    $data = $request->all();

    \Log::debug($data);
    $result = [
        "token" => $data['token'],
        "transactionID" => $data['transactionID'],
        "address" => $data["address"],
        "amount" => $data["amount"],
        "status" => $data["status"],
        "remarks" => $data["remarks"],
    ];

    $payment = Payment::query()
        ->where('transaction_id', $result['transactionID'])
        ->first();

    $dataToHash = md5($payment->transaction_id . $payment->to_wallet_address);

    if ($result['token'] === $dataToHash) {
        //proceed approval
        $payment->update([
            'status' => $result['status'],
            'remarks' => $result['remarks']
        ]);

        if ($payment->status =='Success') {
            $wallet = Wallet::find($payment->wallet_id);

            $wallet->update([
                'balance' => $wallet->balance + $payment->amount
            ]);
        } else {
            PaymentStatus::create([
                'message' => 'Payment with ID ' . $payment->id . ', STATUS is ' . $payment->status
            ]);
        }
    }

    return response()->json(['success' => true, 'message' => 'Deposit Success']);
});

Route::get('/', function () {
    return redirect()->route('login');
});
Route::get('locale/{locale}', function ($locale) {
    App::setLocale($locale);
    Session::put("locale", $locale);

    return redirect()->back();
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
        Route::get('/getWalletBalance', [WalletController::class, 'getWalletBalance'])->name('wallet.getWalletBalance');
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

     Route::prefix('report')->group(function () {
         Route::get('/finance_history', [ReportController::class, 'detail'])->name('report.finance_history');
         Route::get('/getEarningRecord', [ReportController::class, 'getEarningRecord'])->name('report.getEarningRecord');
         Route::get('/getInvestmentRecord', [ReportController::class, 'getInvestmentRecord'])->name('report.getInvestmentRecord');

     });
});

Route::get('/components/buttons', function () {
    return Inertia::render('Components/Buttons');
})->name('components.buttons');

require __DIR__ . '/auth.php';
