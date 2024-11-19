<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Language;
use App\Models\Question;
use App\Models\SubCategory;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\UserSession;
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
        return view("quiz.index", compact('languages', 'categories', 'sub_categories', 'subjects', 'topics'));
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
        // if (!$request->header('Authorization')) return response()->json(['error' => 'Please Provide Session Id']);

        // if (UserSession::where('session_id', explode(" ", $request->header('Authorization'))[1])->first()) {
        $data = $request->all();

        $questionsFirst = $this->getFirstDropdownData($data) ? $this->getFirstDropdownData($data)['questions'] : [];
        $questionsSecond = $this->getSecondDropdownData($data) ? $this->getSecondDropdownData($data)['questions'] : null;

        $language = $this->getFirstDropdownData($data)['language'];
        $categories = $this->getFirstDropdownData($data)['categories'];
        $subcategories = $this->getFirstDropdownData($data)['subcategories'];
        $subjects = $this->getFirstDropdownData($data)['subjects'];
        $topics = $this->getFirstDropdownData($data)['topics'];

        $language2 = $this->getSecondDropdownData($data)['language'] ?? null;
        $categories2 = $this->getSecondDropdownData($data)['categories'] ?? collect();
        $subcategories2 = $this->getSecondDropdownData($data)['subcategories'] ?? collect();
        $subjects2 = $this->getSecondDropdownData($data)['subjects'] ?? collect();
        $topics2 = $this->getSecondDropdownData($data)['topics'] ?? collect();

        // Transform the questions into the desired JSON structure
        $jsonResponse = [];

        $languageName = '<span class="notranslate">' . $language->name . '</span>';
        if ($language2) {
            $languageName .= ' | ' . $language2->name;
        }

        foreach ($categories as $category) {
            $categoryName = '<span class="notranslate">' . $category->name  . '</span>';
            if ($categories2->isNotEmpty()) {
                foreach ($categories2 as $category2) {
                    $combinedCategoryName = $categoryName . ' | ' . $category2->name;
                }
            } else {
                $combinedCategoryName = $categoryName;
            }

            foreach ($subcategories as $subcategory) {
                foreach ($subjects as $subject) {
                    // Filter subjects based on the selected subject
                    if (isset($data['Subject']) && $subject->id != $data['Subject']) {
                        continue;
                    }

                    foreach ($topics as $outkey => $topic) {
                        // Filter topics based on the selected topic
                        if (isset($data['Topic']) && $topic->id != $data['Topic']) {
                            continue;
                        }

                        $subcategoryName = '<span class="notranslate">' . $subcategory->name . '</span>';
                        $subjectName = '<span class="notranslate">' . $subject->name . '</span>';
                        $topicName = '<span class="notranslate">' . $topic->name . '</span>';

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
                            foreach ($topics2 as $innerKey => $topic2) {
                                if ($outkey == $innerKey) {
                                    $combinedTopicName = $topicName . ' | ' . $topic2->name;
                                }
                            }
                        } else {
                            $combinedTopicName = $topicName;
                        }

                        if (!isset($jsonResponse[$languageName])) {
                            $jsonResponse[$languageName] = [];
                        }
                        if (!isset($jsonResponse[$languageName][$combinedCategoryName])) {
                            $jsonResponse[$languageName][$combinedCategoryName] = [];
                        }
                        if (!isset($jsonResponse[$languageName][$combinedCategoryName][$combinedSubcategoryName])) {
                            $jsonResponse[$languageName][$combinedCategoryName][$combinedSubcategoryName] = [];
                        }
                        if (!isset($jsonResponse[$languageName][$combinedCategoryName][$combinedSubcategoryName][$combinedSubjectName])) {
                            $jsonResponse[$languageName][$combinedCategoryName][$combinedSubcategoryName][$combinedSubjectName] = [];
                        }
                        if (!isset($jsonResponse[$languageName][$combinedCategoryName][$combinedSubcategoryName][$combinedSubjectName][$combinedTopicName])) {
                            $jsonResponse[$languageName][$combinedCategoryName][$combinedSubcategoryName][$combinedSubjectName][$combinedTopicName] = [];
                        }

                        if (request()->Topic && request()->Topic_2) {
                            $filteredQuestions = $questionsFirst->filter(function ($question) use ($topic, $topics2) {
                                return $question->topic_id == $topic->id || $topics2->contains('id', $question->topic_id);
                            });
                        } else {
                            $filteredQuestions = $questionsFirst->filter(function ($question) use ($topic, $topics2) {
                                return $question;
                            });
                        }

                        // foreach ($filteredQuestions as $questionFirst) {
                        if (isset($questionsSecond)) {
                            foreach ($questionsSecond as $key => $questionSecond) {
                                if (isset($filteredQuestions[$key]?->photo) && $filteredQuestions[$key]?->photo != 0) {
                                    $img = '<br><img src="https://admin.online2study.in/public/storage/questions/' . $filteredQuestions[$key]->photo . '"/>';
                                } else if (isset($filteredQuestions[$key]->photo_link)) {
                                    $img = '<br><img src="' . $filteredQuestions[$key]->photo_link . '"/>';
                                } else {
                                    $img = '';
                                }

                                $jsonResponse[$languageName][$combinedCategoryName][$combinedSubcategoryName][$combinedSubjectName][$combinedTopicName][] = [
                                    'question' => ('<span class="notranslate">' . htmlspecialchars($filteredQuestions[$key]->question ?? '') . '</span>') . ' | ' . htmlspecialchars($questionSecond->question) . $img,
                                    'options' => [
                                        ('<span class="notranslate">' . htmlspecialchars($filteredQuestions[$key]->option_a ?? '') . '</span>') . ' | ' . htmlspecialchars($questionSecond->option_a),
                                        ('<span class="notranslate">' . htmlspecialchars($filteredQuestions[$key]->option_b ?? '') . '</span>') . ' | ' . htmlspecialchars($questionSecond->option_b),
                                        ('<span class="notranslate">' . htmlspecialchars($filteredQuestions[$key]->option_c ?? '') . '</span>') . ' | ' . htmlspecialchars($questionSecond->option_c),
                                        ('<span class="notranslate">' . htmlspecialchars($filteredQuestions[$key]->option_d ?? '') . '</span>') . ' | ' . htmlspecialchars($questionSecond->option_d),
                                    ],
                                    'answer' => $filteredQuestions[$key]->answer ?? '', // Assuming this field exists
                                    'notes' => $filteredQuestions[$key]->notes ?? '' // Assuming this field exists
                                ];
                            }
                        } else {
                            foreach ($filteredQuestions as $questionFirst) {
                                if (isset($questionFirst->photo) && $questionFirst->photo != 0) {
                                    $img = '<br><img src="https://admin.online2study.in/public/storage/questions/' . $questionFirst->photo . '"/>';
                                } else if ($questionFirst->photo_link) {
                                    $img = '<br><img src="' . $questionFirst->photo_link . '"/>';
                                } else {
                                    $img = '';
                                }

                                $jsonResponse[$languageName][$combinedCategoryName][$combinedSubcategoryName][$combinedSubjectName][$combinedTopicName][] = [
                                    'question_id' => $questionFirst->id,
                                    'question' => ('<span class="notranslate">' . htmlspecialchars($questionFirst->question) . '</span>')  . $img,
                                    'options' => [
                                        '<span class="notranslate">' . htmlspecialchars($questionFirst->option_a) . '</span>',
                                        '<span class="notranslate">' . htmlspecialchars($questionFirst->option_b) . '</span>',
                                        '<span class="notranslate">' . htmlspecialchars($questionFirst->option_c) . '</span>',
                                        '<span class="notranslate">' . htmlspecialchars($questionFirst->option_d) . '</span>',
                                    ],
                                    'answer' => $questionFirst->answer // Assuming this field exists
                                ];
                            }
                        }
                    }
                }
            }
        }

        // Function to recursively remove empty arrays
        function removeEmptyArrays($array)
        {
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
        // } else {
        // return response()->json(['error' => 'Session ID does not Matched']);
        // }
    }

    function getFirstDropdownData($data)
    {
        $languageId = $data['Language'] ?? null;

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

        $questions = $query->with(['subCategory',  'subject', 'topic'])->get();

        $language = Language::find($languageId);

        $categories = isset($categoryId) ? Category::where('id', $categoryId)->get() : Category::where('language_id', $languageId)->get();

        $subcategories = isset($data['SubCategory']) ? SubCategory::where('id', $data['SubCategory'])->get() : SubCategory::where('category_id', $categoryId)->get();

        // Get all the subjects for the subcategories
        $subjects = Subject::whereIn('sub_category_id', $subcategories->pluck('id')->toArray())->get();

        // Get all the topics for the subjects
        $topics = Topic::whereIn('subject_id', $subjects->pluck('id')->toArray())->get();

        return ['language' => $language, 'categories' => $categories, 'subcategories' => $subcategories, 'subjects' => $subjects, 'topics' => $topics, 'questions' => $questions];
    }

    function getSecondDropdownData($data)
    {
        $languageId = $data['Language_2'] ?? null;

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

        $questions = $query->with(['subCategory',  'subject', 'topic'])->get();

        $language = Language::find($languageId);

        $categories = isset($categoryId) ? Category::where('id', $categoryId)->get() : Category::where('language_id', $languageId)->get();

        $subcategories = isset($data['SubCategory_2']) ? SubCategory::where('id', $data['SubCategory_2'])->get() : SubCategory::where('category_id', $categoryId)->get();

        // Get all the subjects for the subcategories
        $subjects = Subject::whereIn('sub_category_id', $subcategories->pluck('id')->toArray())->get();

        // Get all the topics for the subjects
        $topics = isset($data['Topic_2']) ? Topic::where('id', $data['Topic_2'])->get() : Topic::whereIn('subject_id', $subjects->pluck('id')->toArray())->get();

        if ($questions->isEmpty()) {
            return null;
        }

        return ['language' => $language, 'categories' => $categories, 'subcategories' => $subcategories, 'subjects' => $subjects, 'topics' => $topics, 'questions' => $questions];
    }
}
