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
use App\Http\Controllers\QuestionBankController;
use App\Http\Controllers\SuperAdminController;
use Illuminate\Support\Facades\Route;

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

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    Route::resource('languages', LanguagesController::class);

    Route::resource('category', CategoryController::class);

    Route::resource('sub-category', SubCategoryController::class);

    Route::resource('subject', SubjectController::class);

    Route::resource('topic', TopicController::class);

    Route::resource('question', QuestionBankController::class);

    Route::resource('bank-question', QuestionBankApiController::class);

    Route::resource('quiz', QuizController::class);

    Route::resource('cbt', CbtController::class);

    Route::get('question_no', [QuestionBankController::class, 'questionNoExist'])
    ->name('get.question_no');

    Route::get('get-categories/{languageId}', [QuestionBankController::class, 'getCategories']);

    Route::get('get-subcategories/{categoryId}', [QuestionBankController::class, 'getSubCategories']);

    Route::get('get-subjects/{subCategoryId}', [QuestionBankController::class, 'getSubjects']);

    Route::get('get-topics/{subjectId}', [QuestionBankController::class, 'getTopics']);

    Route::get('get-questions-data/{language_id}/{category_id}/{subcategory_id}', [CbtController::class, 'getQuestionsData']);

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
});

require __DIR__ . '/auth.php';
