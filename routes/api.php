<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CbtController;
use App\Http\Controllers\QuestionBankController;
use App\Http\Controllers\QuizController;
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

Route::post('login', [AuthenticatedSessionController::class, 'store']);

Route::middleware('authapi')->group(function () {
    Route::get("quiz", [QuizController::class, 'deploy']);

    Route::get('bank-api', [QuestionBankController::class, 'deploy']);

    Route::get('cbt', [CbtController::class, 'deploy']);
});
