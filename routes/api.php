<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CbtController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuizSettingController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\DigitalNoteController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\GoogleUserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\UserCourseController;
use App\Http\Controllers\RazorpayController;
use App\Http\Controllers\PaymentController;

use App\Http\Controllers\LiveTestController;

use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\ScoreBoardController;
use App\Http\Controllers\QuestionBankController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GooglePlayController;

/* |-------------------------------------------------------------------------- | API Routes |-------------------------------------------------------------------------- | | Here is where you can register API routes for your application. These | routes are loaded by the RouteServiceProvider and all of them will | be assigned to the "api" middleware group. Make something great! | */



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::post('login', [AuthenticatedSessionController::class, 'store']);
Route::post('login', [AuthController::class , 'login']);
Route::post('logout', [AuthController::class , 'logout']);

Route::get("{userId}/{courseId}/quiz", [QuizController::class , 'deploy']);
Route::get('{userId}/{courseId}/quiz/settings', [QuizSettingController::class , 'getSettings']);
Route::post('{userId}/{courseId}/quiz/settings', [QuizSettingController::class , 'updateSettings']);

Route::get('{userId}/{courseId}/bank-api', [QuestionBankController::class , 'deploy']);

Route::get('{userId}/{courseId}/cbt', [CbtController::class , 'deploy']);
Route::get('{userId}/{courseId}/{liveTestId}/live-test', [LiveTestController::class , 'deployApi']);

Route::get('{userId}/{courseId}/video', [VideoController::class , 'formattedAPI']);

Route::middleware('authapi')->group(function () { });

Route::post('reports', [ReportController::class , 'store']);

// Route::get('wallet/history', [WalletHistoryController::class, 'index']);
// Route::post('wallet/add', [WalletHistoryController::class, 'walletAdd']);
// Route    ::post('wallet/charges', [WalletHistoryController::class, 'walletCharges']);

Route::post('scoreboard', [ScoreBoardController::class , 'store']);
Route::get('scoreboard/{userId}', [ScoreBoardController::class , 'show']);

Route::delete('user/{id}/delete', [GoogleUserController::class , 'deleteUser']);
Route::get('user/{id}/profile', [GoogleUserController::class , 'getProfile']);
Route::post('user/{id}/update', [GoogleUserController::class , 'updateUser']);
Route::post('user/{id}/update/code', [GoogleUserController::class , 'updateUserCode']);
// Route::post('user/{id}/update/language/category', [GoogleUserController::class, 'updateLanguageCategory']);

// get category 
Route::get('/get-categories/{language_id}', [CategoryController::class , 'getCategoriesByLanguage']);

// get offer 
Route::get('offers', [OfferController::class , 'getOffersApi']);
// Route::get('digital-notes', [DigitalNoteController::class , 'apiIndex']);
Route::get('{userId}/{courseId}/digital-notes', [DigitalNoteController::class , 'getCourseDigitalNotes']);
Route::get('{userId}/{courseId}/live-tests', [LiveTestController::class , 'getLiveTestsByCourse']);

// get 7. Refer and Welcome Coin
Route::get('sub-category-details/{id}', [SubCategoryController::class , 'getSubCategoryDetailsWithOffers']);

Route::get('courses/offers/{user_id}', [CourseController::class , 'getCoursesWithOffers']);

Route::get('user/courses/{userId}', [UserCourseController::class , 'getUserCourses']);
Route::post('user/courses', [UserCourseController::class , 'assignCourseToUser']);

// Route::post('save-payment', [RazorpayController::class, 'store']);
// Route::get('get-final-amount', [RazorpayController::class, 'getFinalAmount']);

Route::get('Razorpay/initiate', [RazorpayController::class , 'initiatePayment']);
Route::get('Razorpay/callback', [RazorpayController::class , 'handleCallback']);
Route::get('user/{userId}/payments', [PaymentController::class , 'getUserPayments']);

Route::post('store/quiz', [ScoreBoardController::class , 'quizeStore']);
Route::get('show/quiz/{google_user_id}/{sub_category_id}', [ScoreBoardController::class , 'quizeShow']);

Route::post('store/question-bank-count', [ScoreBoardController::class , 'questionCountStore']);
Route::get('show/question-bank-count/{google_user_id}/{sub_category_id}', [ScoreBoardController::class , 'questionCountShow']);

Route::post('store/mock-test', [ScoreBoardController::class , 'mockTestStore']);
Route::get('show/mock-test/{google_user_id}/{sub_category_id}', [ScoreBoardController::class , 'mockTestShow']);

Route::post('store/rank', [ScoreBoardController::class , 'rankStore']);
Route::get('show/rank/{google_user_id}/{live_test_id}', [ScoreBoardController::class , 'rankShow']);

Route::post('update/course/status', [UserCourseController::class , 'updateCourseStatus']);

Route::post('user/video-progress/save', [VideoController::class , 'updateVideoProgress']);

Route::get('user/video-progress/{userId}/{subjectId}', [VideoController::class , 'getVideoProgress']);

// Google Play Billing Routes
Route::post('google-play/verify-subscription', [GooglePlayController::class , 'verifySubscription']);
Route::post('google-play/verify-purchase', [GooglePlayController::class , 'verifyPurchase']);

// Webhook Routes
Route::post('webhooks/google-play', [GooglePlayController::class , 'handleRTDN']);
Route::post('webhooks/razorpay', [RazorpayController::class , 'handleWebhook']);

// User Notifications
Route::get('user/{userId}/notifications', [NotificationController::class , 'apiIndex']);
Route::post('notifications/mark-read', [NotificationController::class , 'markAsRead']);
Route::post('notifications/delete', [NotificationController::class , 'deleteNotifications']);