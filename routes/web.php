<?php

use App\Http\Controllers\DepositController;
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

Route::post('upload/tmp_img', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'upload']);
Route::post('upload/image-revert', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'image_revert']);

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/Earn', function () {
    return Inertia::render('Earn/Earn');
})->middleware(['auth'])->name('earn');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('wallet')->group(function () {
        Route::get('/details', [WalletController::class, 'details'])->name('wallet.details');
        Route::get('/getTransaction', [WalletController::class, 'getTransaction'])->name('wallet.getTransaction');
        Route::post('/deposit', [DepositController::class, 'deposit'])->name('wallet.deposit');
    });
});

Route::get('/components/buttons', function () {
    return Inertia::render('Components/Buttons');
})->name('components.buttons');

require __DIR__ . '/auth.php';
