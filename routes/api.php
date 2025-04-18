<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CbtController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\GoogleUserController;
use App\Http\Controllers\ScoreBoardController;
use App\Http\Controllers\QuestionBankController;
use App\Http\Controllers\WalletHistoryController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

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

Route::post('login', [AuthenticatedSessionController::class, 'store']);

Route::middleware('authapi')->group(function () {
    Route::get("quiz", [QuizController::class, 'deploy']);

    Route::get('bank-api', [QuestionBankController::class, 'deploy']);

    Route::get('cbt', [CbtController::class, 'deploy']);
});

Route::get('/reports', [ReportController::class, 'index']);
Route::post('/reports', [ReportController::class, 'store']);

// wallet
Route::get('/wallet-history', [WalletHistoryController::class, 'index']);
Route::post('/wallet-add', [WalletHistoryController::class, 'walletAdd']);
Route::post('/wallet-charges', [WalletHistoryController::class, 'walletCharges']);

Route::post('/scoreboard', [ScoreBoardController::class, 'store']);
Route::get('/scoreboard/{userId}', [ScoreBoardController::class, 'show']);

Route::post('user/delete/{id}', [GoogleUserController::class, 'deleteUser']);
Route::get('user/{id}/profile', [GoogleUserController::class, 'getProfile']);
Route::post('user/update/{id}', [GoogleUserController::class, 'updateUser']);
Route::post('user/{id}/update-language-category/', [GoogleUserController::class, 'updateLanguageCategory']);

// get offer 
Route::get('offers', [OfferController::class, 'getOffersApi']);

// get 7. Refer and Welcome Coin
Route::get('coins', [SettingController::class, 'getSettingsApi']);
