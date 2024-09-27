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

class CbtController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $languages = Language::all();
        return view("cbt.index", compact('languages')); 
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

    public function getQuestionsData($language_id, $category_id, $subcategory_id){
        $subjects = Subject::where('sub_category_id', $subcategory_id)->get();

        foreach ($subjects as $subject) {
            # code...
            $questions = Question::where('subject_id', $subject->id) ->where('language_id', $language_id)->where('category_id',  $category_id)->get()->toArray();
            $questions = count($questions);
            $subject->questions = $questions;
        }

        return response()->json(['subjects' => $subjects]);
    }

    public function deploy(Request $request)
    {
        $data = $request->all();

        $apiLink = $data['api_link'];
        // Example: http://localhost:8000/api/quiz?Language=1,2&Language-2=3&Category=1&SubCategory=1&Subject=1&Topic=1

        // Parse the URL to get the query string
        $urlComponents = parse_url($apiLink);
        $queryString = $urlComponents['query'] ?? '';

        // Parse the query string into an associative array
        $params = [];
        parse_str($queryString, $params);

        // Now $params contains all the parameters from the URL
        // Example: ['Language' => '1,2', 'Language-2' => '3', 'Category' => '1', 'SubCategory' => '1', 'Subject' => '1', 'Topic' => '1']
    
        // Fetch the category based on the 'Category' parameter
        $categoryId = $params['Category'] ?? null;
        if (!$categoryId) {
            return response()->json(['error' => 'Category parameter is missing'], 400);
        }
    
        // Fetch questions based on the parameters
        $query = Question::query()->where('category_id', $categoryId);
    
        // Handle both "Language" and "Language-2" parameters
        $languages = [];
        if (isset($params['Language'])) {
            $languages = array_merge($languages, explode(',', $params['Language']));
        }
        if (isset($params['Language-2'])) {
            $languages = array_merge($languages, explode(',', $params['Language-2']));
        }
        if (!empty($languages)) {
            $query->whereIn('language_id', $languages);
        }
        
        if (isset($params['SubCategory'])) {
            $query->where('sub_category_id', $params['SubCategory']);
        }
        
        $questions = $query->get();
            
        $translated_questions = [];
        
        foreach ($languages as $languageId) {
            $language = Language::findOrFail($languageId);
            $translated_questions[$language->name] = [];
        
            $filteredQuestions = $questions->filter(function ($question) use ($languageId) {
                return $question->language_id == $languageId;
            });
        
            foreach ($filteredQuestions as $question) {
                $translated_questions[$language->name][$question->id] = [
                    'question' => htmlspecialchars($question->question),
                    'option_a' => htmlspecialchars($question->option_a),
                    'option_b' => htmlspecialchars($question->option_b),
                    'option_c' => htmlspecialchars($question->option_c),
                    'option_d' => htmlspecialchars($question->option_d),
                    'answer' => $question->answer,
                ];
            }
        }
        
        // dd($query->toRawSql(), $translated_questions, $languages);
        // Transform the questions into the desired JSON structure
        $jsonResponse = [];

        // foreach ($translated_questions as $language => $question) {
        //     $jsonResponse = [
        //         'question' => htmlspecialchars($question['question']),
        //     ];
        // }

        foreach ($questions as $question) {
            $jsonResponse[] = [
                htmlspecialchars($question->question),
                htmlspecialchars($question->option_a),
                htmlspecialchars($question->option_b),
                htmlspecialchars($question->option_c),
                htmlspecialchars($question->option_d),
                $question->answer // Assuming this field exists and is a letter like 'A', 'B', 'C', or 'D'
            ];
        }

        // Return the JSON response
        return response()->json($jsonResponse);
    }
}
