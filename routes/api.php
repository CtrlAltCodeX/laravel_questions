<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CbtController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\GoogleUserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\UserCourseController;
use App\Http\Controllers\PaymentController;

use App\Http\Controllers\SubCategoryController;
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

Route::get("{userId}/{CourseId}/quiz", [QuizController::class, 'deploy']);

Route::get('{userId}/{CourseId}/bank-api', [QuestionBankController::class, 'deploy']);

Route::get('{userId}/{CourseId}/cbt', [CbtController::class, 'deploy']);

Route::middleware('authapi')->group(function () {});

Route::post('reports', [ReportController::class, 'store']);

// wallet
Route::get('wallet/history', [WalletHistoryController::class, 'index']);
// Route::post('wallet/add', [WalletHistoryController::class, 'walletAdd']);
Route::post('wallet/charges', [WalletHistoryController::class, 'walletCharges']);

Route::post('scoreboard', [ScoreBoardController::class, 'store']);
Route::get('scoreboard/{userId}', [ScoreBoardController::class, 'show']);

Route::delete('user/{id}/delete', [GoogleUserController::class, 'deleteUser']);
Route::get('user/{id}/profile', [GoogleUserController::class, 'getProfile']);
Route::post('user/{id}/update', [GoogleUserController::class, 'updateUser']);
// Route::post('user/{id}/update/language/category', [GoogleUserController::class, 'updateLanguageCategory']);

// get category 
Route::get('/get-categories/{language_id}', [CategoryController::class, 'getCategoriesByLanguage']);

// get offer 
Route::get('offers', [OfferController::class, 'getOffersApi']);

// get 7. Refer and Welcome Coin
Route::get('sub-category-details/{id}', [SubCategoryController::class, 'getSubCategoryDetailsWithOffers']);

Route::get('courses/offers/{user_id}', [CourseController::class, 'getCoursesWithOffers']);

Route::get('user/courses/{userId}', [UserCourseController::class, 'getUserCourses']);
Route::post('user/courses', [UserCourseController::class, 'assignCourseToUser']);

Route::post('save-payment', [PaymentController::class, 'store']);
Route::get('get-final-amount', [PaymentController::class, 'getFinalAmount']);

Route::post('quize/store', [ScoreBoardController::class, 'quizeStore']);
Route::get('/quiz/show/{google_user_id}/{sub_category_id}', [ScoreBoardController::class, 'quizeShowData']);

Route::post('question-bank-count/store', [ScoreBoardController::class, 'questionCountStore']);
Route::get('question-bank-count/{google_user_id}/{sub_category_id}', [ScoreBoardController::class, 'questionCountShow']);

Route::post('mock-test/store', [ScoreBoardController::class, 'mockTestStore']);
Route::get('mock-test/{google_user_id}', [ScoreBoardController::class, 'mockTestShow']);

Route::post('update/course/status', [UserCourseController::class, 'updateCourseStatus']);
