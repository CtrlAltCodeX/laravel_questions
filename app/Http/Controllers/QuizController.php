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
        $data = $request->all();

        $apiLink = $data['api_link'];
        // Example: http://localhost:8000/api/quiz?Language=1&Category=1&SubCategory=1&Subject=1&Topic=1

        // Parse the URL to get the query string
        $urlComponents = parse_url($apiLink);
        $queryString = $urlComponents['query'] ?? '';

        // Parse the query string into an associative array
        $params = [];
        parse_str($queryString, $params);

        // Now $params contains all the parameters from the URL
        // Example: ['Language' => '1', 'Category' => '1', 'SubCategory' => '1', 'Subject' => '1', 'Topic' => '1']
    
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

        //get all the topics for the category
        $subcategories = SubCategory::where('category_id', $categoryId)->get();
        // get all the subjects for thhe subcategories
        $subjects = Subject::whereIn('sub_category_id', $subcategories->pluck('id'))->get();
        // get all the topics for the subjects
        $topics = Topic::whereIn('subject_id', $subjects->pluck('id'))->get();
    
        // Transform the questions into the desired JSON structure
        $jsonResponse = [];
        foreach ($subjects as $subject) {
            $subjectName = $subject->name;
            $jsonResponse[$subjectName] = [];
        
            foreach ($topics as $topic) {
                if ($topic->subject_id == $subject->id) {
                    $topicName = $topic->name;
                    $jsonResponse[$subjectName][$topicName] = [];
        
                    $filteredQuestions = $questions->filter(function ($question) use ($topic) {
                        return $question->topic_id == $topic->id;
                    });
        
                    foreach ($filteredQuestions as $question) {
                        $jsonResponse[$subjectName][$topicName][] = [
                            'question' => htmlspecialchars($question->question),
                            'options' => [
                                htmlspecialchars($question->option_a),
                                htmlspecialchars($question->option_b),
                                htmlspecialchars($question->option_c),
                                htmlspecialchars($question->option_d),
                            ],
                            'answer' => $question->answer // Assuming this field exists
                        ];
                    }
                }
            }
        }
        // Return the JSON response
        return response()->json($jsonResponse);
    }
}
