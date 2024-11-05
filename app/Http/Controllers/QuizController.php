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

        // $apiLink = $data['api_link'];
        // // Example: http://localhost:8000/api/quiz?Language=1&Category=1&SubCategory=1&Subject=1&Topic=1

        // // Parse the URL to get the query string
        // $urlComponents = parse_url($apiLink);
        // $queryString = $urlComponents['query'] ?? '';

        // // Parse the query string into an associative array
        // $params = [];
        // parse_str($queryString, $params);

        // Now $params contains all the parameters from the URL
        // Example: ['Language' => '1', 'Category' => '1', 'SubCategory' => '1', 'Subject' => '1', 'Topic' => '1']

        $questionsFirst = $this->getFirstDropdownData($data) ? $this->getFirstDropdownData($data)['questions'] : [];
        $questionsSecond = $this->getSecondDropdownData($data) ? $this->getSecondDropdownData($data)['questions'] : null;
        
        $subcategories = $this->getFirstDropdownData($data)['subcategories'];
        $subjects = $this->getFirstDropdownData($data)['subjects'];
        $topics = $this->getFirstDropdownData($data)['topics'];
        
        $subcategories2 = $this->getSecondDropdownData($data)['subcategories'] ?? collect();
        $subjects2 = $this->getSecondDropdownData($data)['subjects'] ?? collect();
        $topics2 = $this->getSecondDropdownData($data)['topics'] ?? collect();
        
        // Transform the questions into the desired JSON structure
        $jsonResponse = [];
        
        foreach ($subcategories as $subcategory) {
            foreach ($subjects as $subject) {
                // Filter subjects based on the selected subject
                if (isset($data['Subject']) && $subject->id != $data['Subject']) {
                    continue;
                }
        
                foreach ($topics as $topic) {
                    // Filter topics based on the selected topic
                    if (isset($data['Topic']) && $topic->id != $data['Topic']) {
                        continue;
                    }
        
                    $subcategoryName = $subcategory->name;
                    $subjectName = $subject->name;
                    $topicName = $topic->name;
        
                    if ($subcategories2->isNotEmpty()) {
                        foreach ($subcategories2 as $subcategory2) {
                            $combinedSubcategoryName = $subcategoryName . ' | ' . $subcategory2->name;
                        }
                    } else {
                        $combinedSubcategoryName = $subcategoryName;
                    }
        
                    if ($subjects2->isNotEmpty()) {
                        foreach ($subjects2 as $subject2) {
                            $combinedSubjectName = $subjectName . ' | ' . $subject2->name;
                        }
                    } else {
                        $combinedSubjectName = $subjectName;
                    }
        
                    if ($topics2->isNotEmpty()) {
                        foreach ($topics2 as $topic2) {
                            $combinedTopicName = $topicName . ' | ' . $topic2->name;
                        }
                    } else {
                        $combinedTopicName = $topicName;
                    }
        
                    if (!isset($jsonResponse[$combinedSubcategoryName])) {
                        $jsonResponse[$combinedSubcategoryName] = [];
                    }
                    if (!isset($jsonResponse[$combinedSubcategoryName][$combinedSubjectName])) {
                        $jsonResponse[$combinedSubcategoryName][$combinedSubjectName] = [];
                    }
                    if (!isset($jsonResponse[$combinedSubcategoryName][$combinedSubjectName][$combinedTopicName])) {
                        $jsonResponse[$combinedSubcategoryName][$combinedSubjectName][$combinedTopicName] = [];
                    }
        
                    $filteredQuestions = $questionsFirst->filter(function ($question) use ($topic, $topics2) {
                        return $question->topic_id == $topic->id || $topics2->contains('id', $question->topic_id);
                    });
        
                    foreach ($filteredQuestions as $questionFirst) {
                        if (isset($questionsSecond)) {
                            foreach ($questionsSecond as $questionSecond) {
                                $jsonResponse[$combinedSubcategoryName][$combinedSubjectName][$combinedTopicName][] = [
                                    'question' => htmlspecialchars($questionFirst->question) . ' | ' . htmlspecialchars($questionSecond->question),
                                    'options' => [
                                        htmlspecialchars($questionFirst->option_a) . ' | ' . htmlspecialchars($questionSecond->option_a),
                                        htmlspecialchars($questionFirst->option_b) . ' | ' . htmlspecialchars($questionSecond->option_b),
                                        htmlspecialchars($questionFirst->option_c) . ' | ' . htmlspecialchars($questionSecond->option_c),
                                        htmlspecialchars($questionFirst->option_d) . ' | ' . htmlspecialchars($questionSecond->option_d),
                                    ],
                                    'answer' => $questionFirst->answer // Assuming this field exists
                                ];
                            }
                        } else {
                            $jsonResponse[$combinedSubcategoryName][$combinedSubjectName][$combinedTopicName][] = [
                                'question_id' => $questionFirst->id,
                                'question' => htmlspecialchars($questionFirst->question),
                                'options' => [
                                    htmlspecialchars($questionFirst->option_a),
                                    htmlspecialchars($questionFirst->option_b),
                                    htmlspecialchars($questionFirst->option_c),
                                    htmlspecialchars($questionFirst->option_d),
                                ],
                                'answer' => $questionFirst->answer // Assuming this field exists
                            ];
                        }
                    }
                }
            }
        }

        // Function to recursively remove empty arrays
        function removeEmptyArrays($array) {
            foreach ($array as $key => &$value) {
                if (is_array($value)) {
                    $value = removeEmptyArrays($value);
                    if (empty($value)) {
                        unset($array[$key]);
                    }
                }
            }
            return $array;
        }

        // Remove empty arrays from the JSON response
        $jsonResponse = removeEmptyArrays($jsonResponse);
        
        // Return the JSON response
        return response()->json($jsonResponse);
    }


    function getFirstDropdownData($data){
        $categoryId = $data['Category'] ?? null;
        if (!$categoryId) {
            return response()->json(['error' => 'Category parameter is missing'], 400);
        }
        
        // Fetch questions based on the parameters
        $query = Question::query()->where('category_id', $categoryId);
    
        if (isset($data['Language'])) {
            $query->where('language_id', $data['Language']);
        }
        if (isset($data['SubCategory'])) {
            $query->where('sub_category_id', $data['SubCategory']);
        }
        if (isset($data['Subject'])) {
            $query->where('subject_id', $data['Subject']);
        }
        if (isset($data['Topic'])) {
            $query->where('topic_id', $data['Topic']);
        }
    
        $questions = $query->with([ 'subCategory',  'subject', 'topic' ])->get();
        
        $subcategories = SubCategory::where('category_id', $categoryId)->get();

        // Get all the subjects for the subcategories
        $subjects = Subject::whereIn('sub_category_id', isset($data['SubCategory']) ? [$data['SubCategory']] : $subcategories->pluck('id'))->get();

        // Get all the topics for the subjects
        $topics = Topic::whereIn('subject_id', isset($data['Subject']) ? [$data['Subject']] : $subjects->pluck('id'))->get();

        return [ 'subcategories' => $subcategories, 'subjects' => $subjects, 'topics' => $topics, 'questions' => $questions];
    }

    function getSecondDropdownData($data){
        $categoryId = $data['Category_2'] ?? null;

        if (!$categoryId) {
            return null;
        }

        // Fetch questions based on the parameters
        $query = Question::query()->where('category_id', $categoryId);

        if (isset($data['Language_2'])) {
            $query->where('language_id', $data['Language_2']);
        }

        if (isset($data['SubCategory_2'])) {
            $query->where('sub_category_id', $data['SubCategory_2']);
        }

        if (isset($data['Subject_2'])) {
            $query->where('subject_id', $data['Subject_2']);
        }

        if (isset($data['Topic_2'])) {
            $query->where('topic_id', $data['Topic_2']);
        }

        $questions = $query->with([ 'subCategory',  'subject', 'topic' ])->get();

        $subcategories = SubCategory::where('category_id', $categoryId)->get();

        // Get all the subjects for the subcategories
        $subjects = Subject::whereIn('sub_category_id', isset($data['SubCategory_2']) ? [$data['SubCategory_2']] : $subcategories->pluck('id'))->get();

        // Get all the topics for the subjects
        $topics = Topic::whereIn('subject_id', isset($data['Subject_2']) ? [$data['Subject_2']] : $subjects->pluck('id'))->get();

        if($questions->isEmpty()){
            return null;
        }

        return [ 'subcategories' => $subcategories, 'subjects' => $subjects, 'topics' => $topics, 'questions' => $questions];
    }
}
