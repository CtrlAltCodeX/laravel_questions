<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LanguagesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\QuestionBankController;
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
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('languages', LanguagesController::class);

    Route::resource('category', CategoryController::class);

    Route::resource('sub-category', SubCategoryController::class);

    Route::resource('subject', SubjectController::class);

    Route::resource('topic', TopicController::class);

    Route::resource('question', QuestionBankController::class);

    Route::get('/get-categories/{languageId}', [QuestionBankController::class, 'getCategories']);

    Route::get('/get-subcategories/{categoryId}', [QuestionBankController::class, 'getSubCategories']);

    Route::get('/get-subjects/{subCategoryId}', [QuestionBankController::class, 'getSubjects']);

    Route::get('/get-topics/{subjectId}', [QuestionBankController::class, 'getTopics']);

    Route::get('questions', [QuestionBankController::class, 'getQuestions'])
        ->name("questions");

    Route::get('questions/export', [QuestionBankController::class, 'export'])->name('questions.export');
    Route::post('questions/import', [QuestionBankController::class, 'import'])->name('questions.import');

    Route::post('/question/{id}/delete', [QuestionBankController::class, 'destroyQuestion']);

    Route::get('users', [ProfileController::class, 'users'])
        ->name('users.index');
});

require __DIR__ . '/auth.php';
