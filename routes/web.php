<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CbtController;
use App\Http\Controllers\LanguagesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestionBankApiController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\QuestionBankController;
use App\Http\Controllers\SuperAdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\WalletHistoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ScoreBoardController;
use App\Http\Controllers\SettingController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('languages.index');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile/{id}', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::put('/profile/{id}', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::put('/admin-profile/{id}', [SuperAdminController::class, 'update'])
        ->name('admin-profile.update');

    Route::delete('/profile/{id}', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    Route::put('/users/update-coins-status/{id}', [ProfileController::class, 'updateCoinsAndStatus'])
        ->name('users.updateCoinsAndStatus');


    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard.total.count');
    Route::resource('languages', LanguagesController::class);

    Route::resource('category', CategoryController::class);
    Route::get('category/export/file', [CategoryController::class, 'export'])->name('category.export');
    Route::get('category/sample/file', [CategoryController::class, 'sample'])->name('category.sample');
    Route::post('category/import/file', [CategoryController::class, 'import'])->name('category.import');


    Route::resource('sub-category', SubCategoryController::class);
    Route::get('sub-category/export/file', [SubCategoryController::class, 'export'])->name('sub-category.export');
    Route::get('sub-category/sample/file', [SubCategoryController::class, 'sample'])->name('sub-category.sample');
    Route::post('sub-category/import/file', [SubCategoryController::class, 'import'])->name('sub-category.import');


    Route::resource('subject', SubjectController::class);
    Route::get('subject/export/file', [SubjectController::class, 'export'])->name('subject.export');
    Route::get('subject/sample/file', [SubjectController::class, 'sample'])->name('subject.sample');
    Route::post('subject/import/file', [SubjectController::class, 'import'])->name('subject.import');

    Route::resource('topic', TopicController::class);
    Route::resource('offers', OfferController::class);
    Route::resource('videos', VideoController::class);
    Route::get('videos/export/file', [VideoController::class, 'export'])->name('videos.export');
    Route::post('videos/import/file', [VideoController::class, 'import'])->name('videos.import');

    Route::resource('/courses', CourseController::class);

    Route::get('/get-subjects', [CourseController::class, 'getSubjects']);
    Route::get('/reports', [ReportController::class, 'webindex'])->name('reports.index');
    Route::get('/WalletHistory', [WalletHistoryController::class, 'webindex'])->name('WalletHistory.index');
    Route::put('/reports/update/{VideoId}/{id}', [ReportController::class, 'updateVideo'])->name('reports.updateVideo');
    Route::put('/reports/updateQuestion/{QUestionId}/{id}', [ReportController::class, 'updateQuestion'])->name('reports.updateQuestion');
    Route::delete('/reports/{id}', [ReportController::class, 'destroy'])->name('reports.destroy');

    Route::get('/reports/getVideo_question', [ReportController::class, 'edit'])->name('reports.edit');

    Route::get('/scoreboard', [ScoreBoardController::class, 'index'])->name('ScoreBoard.index');
    Route::get('/quize-practice/{google_user_id}', [ScoreBoardController::class, 'quizeshow']);
Route::get('/question-bank-count-AllData/{google_user_id}', [ScoreBoardController::class, 'questioncountshowAllData']);
    Route::get('/mock-test/{google_user_id}', [ScoreBoardController::class, 'mockTestShow']);

    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'store'])->name('settings.store');

    Route::post('/settings/quiz/save', [SettingController::class, 'saveQuiz'])->name('settings.quiz.save');

    Route::post('/settings/cbt/save', [SettingController::class, 'saveCbt'])->name('settings.cbt.save');

    Route::get('topic/export/file', [TopicController::class, 'export'])->name('topic.export');
    Route::get('topic/sample/file', [TopicController::class, 'sample'])->name('topic.sample');
    Route::post('topic/import/file', [TopicController::class, 'import'])->name('topic.import');

    Route::resource('question', QuestionBankController::class);

    Route::resource('bank-question', QuestionBankApiController::class);

    Route::resource('quiz', QuizController::class);

    Route::resource('cbt', CbtController::class);

    Route::get('question_no', [QuestionBankController::class, 'questionNoExist'])
        ->name('get.question_no');

    Route::get('get-categories/{languageId}', [QuestionBankController::class, 'getCategories']);

    Route::get('get-subcategories/{categoryId}', [QuestionBankController::class, 'getSubCategories']);

    Route::get('get-subjects/{subCategoryId}', [QuestionBankController::class, 'getSubjects']);

    Route::get('get-subcategories-from-subject/{subjectId}', [QuestionBankController::class, 'getSubCategoriesFromSubject']);

    Route::get('get-topics/{subjectId}', [QuestionBankController::class, 'getTopics']);

    Route::get('get-questions-data/{language_id}/{category_id}/{subcategory_id}/{language2_id}/{category2_id}/{subcategory2_id}', [CbtController::class, 'getQuestionsData']);

    Route::group(['prefix' => 'questions'], function () {
        Route::get('', [QuestionBankController::class, 'getQuestions'])
            ->name("questions");

        Route::get('export', [QuestionBankController::class, 'export'])
            ->name('questions.export');

        Route::post('import', [QuestionBankController::class, 'import'])
            ->name('questions.import');

        Route::post('/{id}/delete', [QuestionBankController::class, 'destroyQuestion']);

        Route::post('/{id}/translation-delete', [QuestionBankController::class, 'destroyTranslationQuestion']);

        Route::post('bulk-delete', [QuestionBankController::class, 'bulkDelete'])
            ->name("question.bulkDelete");
    });

    Route::group(['prefix' => 'quiz'], function () {
        Route::post('deploy', [QuizController::class, 'deploy'])
            ->name('quiz.deploy');
    })->middleware('auth:sanctum');

    Route::group(['prefix' => 'cbt'], function () {
        Route::post('deploy', [CbtController::class, 'deploy'])
            ->name('cbt.deploy');
    })->middleware('auth:sanctum');

    Route::get('users', [ProfileController::class, 'users'])
        ->name('users.index');

    Route::get('super-admin', [SuperAdminController::class, 'super_admin'])
        ->name('super-admin.index');

    Route::get('super-admin/create', [SuperAdminController::class, 'show'])
        ->name('super-admin.create');

    Route::post('super-admin/store', [SuperAdminController::class, 'store'])
        ->name('super-admin.store');

    Route::get('/admin-profile/{id}', [SuperAdminController::class, 'edit'])
        ->name('admin-profile.edit');
});

require __DIR__ . '/auth.php';
