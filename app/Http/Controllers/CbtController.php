<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Language;
use App\Models\Question;
use App\Models\SubCategory;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\TranslatedQuestions;
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
        $categories = Category::all();
        return view("cbt.index", compact('languages', 'categories')); 
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
        $translated_questions_query = TranslatedQuestions::query();
    
        // Handle both "Language" and "Language-2" parameters
        $languages = [];
        if (isset($params['Language'])) {
            $languages = array_merge($languages, explode(',', $params['Language']));
        }
        if (isset($params['Language-2'])) {
            $languages = array_merge($languages, explode(',', $params['Language-2']));
        }
        if (!empty($languages)) {
            $translated_questions_query->whereIn('language_id', $languages);
        }
        
        if (isset($params['SubCategory'])) {
            $query->where('sub_category_id', $params['SubCategory']);
        }
        
        $questions = $query->with([ 'category',  'subCategory', 'subject', 'topic', 'translatedQuestions'])->get();


        $translatedQuestions = $translated_questions_query
            ->whereIn('question_id', $questions->pluck('id'))
            ->with(['question', 'language'])
            ->get();

        $jsonResponse = [];
                    
        // Transform the questions into the desired JSON structure
        foreach ($data['subjects'] as $subjectName => $numberOfQuestions) {
            
            $subject = Subject::where('name', str_replace('_', ' ', $subjectName))->first();
            
            // shuffle the questions according to the subject and number of questions
            $shuffledSubjectQuestions =  $questions->where('subject_id', $subject->id)->shuffle()->take($numberOfQuestions);
            
            $subjectEntry = [
                'subject' => $subject->name,
                'questions' => []
            ];

            foreach ($shuffledSubjectQuestions as $question) {
                
                if (count($question->translatedQuestions)) {
                    $translatedQuestions = $question->translatedQuestions;
                    $questionText = '';
                    $optionA = '';
                    $optionB = '';
                    $optionC = '';
                    $optionD = '';
            
                    foreach ($languages as $languageId) {
                        $translatedQuestion = $translatedQuestions->where('language_id', $languageId)->first();        
                        if ($translatedQuestion) {
                            $questionText .= htmlspecialchars($translatedQuestion->question_text) . ' </br> ';
                            $optionA .= htmlspecialchars($translatedQuestion->option_a) . ' </br> ';
                            $optionB .= htmlspecialchars($translatedQuestion->option_b) . ' </br> ';
                            $optionC .= htmlspecialchars($translatedQuestion->option_c) . ' </br> ';
                            $optionD .= htmlspecialchars($translatedQuestion->option_d) . ' </br> ';
                        }
                    }
            
                    // Remove the trailing ' </br> ' from each string
                    $questionText = rtrim($questionText, ' </br> ');
                    $optionA = rtrim($optionA, ' </br> ');
                    $optionB = rtrim($optionB, ' </br> ');
                    $optionC = rtrim($optionC, ' </br> ');
                    $optionD = rtrim($optionD, ' </br> ');
                    
                    $subjectEntry['questions'][] = [
                        $questionText,
                        $optionA,
                        $optionB,
                        $optionC,
                        $optionD,
                        $question['answer'] // Assuming this field exists and is a letter like 'A', 'B', 'C', or 'D'
                    ];
                }

            }

            if (!empty($subjectEntry['questions'])) {
                $jsonResponse[] = $subjectEntry;
            }
        }
        

        // Return the JSON response
        return response()->json($jsonResponse);
    }
}
