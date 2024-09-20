<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Language;
use App\Models\Question;
use App\Models\SubCategory;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //$accessToken = session('access_token');
        $languages = Language::all();
        $categories = Category::all();
        $sub_categories = SubCategory::all();
        $subjects = Subject::all();
        $topics = Topic::all();
        return view("quiz.index", compact('languages', 'categories','sub_categories','subjects', 'topics'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function deploy(Request $request)
    {
        $params = $request->all();
    
        // Fetch the category based on the 'Category' parameter
        $categoryId = $params['Category'] ?? null;
        if (!$categoryId) {
            return response()->json(['error' => 'Category parameter is missing'], 400);
        }
    
        $category = Category::findOrFail($categoryId);
    
        // Fetch questions based on the parameters
        $query = Question::query()->where('category_id', $categoryId);
    
        if (isset($params['Language'])) {
            $query->where('language_id', $params['Language']);
        }
        if (isset($params['SubCategory'])) {
            $query->where('sub_category_id', $params['SubCategory']);
        }
        if (isset($params['Subject'])) {
            $query->where('subject_id', $params['Subject']);
        }
        if (isset($params['Topic'])) {
            $query->where('topic_id', $params['Topic']);
        }
    
        $questions = $query->get();
    
        // Transform the questions into the desired JSON structure
        $jsonResponse = [
            'category' => "<b class='unlock-btn'><i class='fa fa-unlock-alt'></i> Unlock</b><br>{$category->name}",
            'quizWrap' => $questions->map(function ($question) {
                return [
                    'question' => "{$question->question_numbe}" . htmlspecialchars($question->question) . (isset($question->photoLink) ? "<br><img src='" . htmlspecialchars($question->photoLink) . "'>" : ""),
                    'options' => [
                        "(A) {$question->option_a}",
                        "(B) {$question->option_b}",
                        "(C) {$question->option_c}",
                        "(D) {$question->option_d}",
                    ],
                    'answer' => $question->answer // Assuming this field exists
                ];
            })->toArray()
        ];    
        // Return the JSON response
        return response()->json($jsonResponse);
    }
}
