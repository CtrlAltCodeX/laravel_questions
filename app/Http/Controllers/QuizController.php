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
use App\Models\Quiz;
use App\Models\Course;

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

        $dropdown_list = [
            'Select Language' => $languages,
            'Select Category' => [],
            'Select SubCategory' => [],
            'Select Subject' => [],
            'Select Topic' => [],
        ];

        return view("quiz.index", compact('languages', 'categories', 'sub_categories', 'subjects', 'topics', 'dropdown_list'));
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

    public function deploy(Request $request, $userId, $courseId)
    {
        if (!$request->header('Authorization')) return response()->json(['error' => 'Please Provide Session Id'], 400);

        if (UserSession::where('session_id', explode(" ", $request->header('Authorization'))[1])->first()) {
            $data = $request->all();
            $course = Course::find($courseId);
            if ($course->language) {
                $category = Category::find($data['Category']);
                $subcategory = SubCategory::find($data['SubCategory']);
                $subject = Subject::find($data['Subject']);

                $data['Language_2'] = 1;
                $data['Category_2'] = $category->parent_id;
                $data['SubCategory_2'] = $subcategory->parent_id;
                $data['Subject_2'] = $subject->parent_id;
                // $data['Topic_2'] = 1;
            }

            $questionsFirst = $this->getFirstDropdownData($data, $course) ? $this->getFirstDropdownData($data, $course)['questions'] : [];
            $questionsSecond = $this->getSecondDropdownData($data) ? $this->getSecondDropdownData($data)['questions'] : null;

            $language = $this->getFirstDropdownData($data, $course)['language'];
            $categories = $this->getFirstDropdownData($data, $course)['categories'][0];
            $subcategories = $this->getFirstDropdownData($data, $course)['subcategories'][0];
            $subjects = $this->getFirstDropdownData($data, $course)['subjects'][0];
            $topics = $this->getFirstDropdownData($data, $course)['topics'];

            $language2 = $this->getSecondDropdownData($data)['language'] ?? null;
            $categories2 = $this->getSecondDropdownData($data)['categories'] ?? [];
            $subcategories2 = $this->getSecondDropdownData($data)['subcategories'] ?? [];
            $subjects2 = $this->getSecondDropdownData($data)['subjects'] ?? [];
            $topics2 = $this->getSecondDropdownData($data)['topics'] ?? [];

            // Transform the questions into the desired JSON structure
            $jsonResponse = [];

            $languageName = '<span class="notranslate">' . $language->name . '</span>';
            if ($language2) {
                $languageName .= ' | ' . $language2->name;
            }

            $categoryName = '<span class="notranslate">' . $categories->name . '</span>';
            if (count($categories2)) {
                $categoryName .= ' | ' . $categories2[0]->name;
            }

            $subcategoryName = '<span class="notranslate">' . $subcategories->name . '</span>';
            if (count($subcategories2)) {
                $subcategoryName .= ' | ' . $subcategories2[0]->name;
            }

            $subjectName = '<span class="notranslate">' . $subjects->name . '</span>';
            if (count($subjects2)) {
                $subjectName .= ' | ' . $subjects2[0]->name;
            }

            $i = 0;
            foreach ($topics as $outkey => $topic) {
                $topicsName = '<span class="notranslate">' . $topic->name . "</span>";
                if (count($topics2)) {
                    $topicsName .= ' | ' . ($topics2[$outkey]->name ?? '');
                }

                $questionArray = [];
                $questionAccTop = [];

                foreach ($questionsFirst as $innerKey => $question) {
                    if ($question->topic_id == $topic->id) {
                        $questionArray[] = $question;
                    }
                }

                foreach ($questionArray as $key => $getQuestions) {
                    $img = isset($getQuestions->photo) && $getQuestions->photo != 0
                        ? '<br><img src="https://iti.online2study.in/storage/questions/' . $getQuestions->photo . '"/>'
                        : (isset($getQuestions->photo_link)
                            ? '<br><img src="' . $getQuestions->photo_link . '"/>'
                            : '');

                    if (isset($questionsSecond[$i]->question_number)){
                        if ($getQuestions->question_number == $questionsSecond[$i]->question_number) {
                            $questionAccTop[$key]['question'] = '<span class="notranslate">' . $getQuestions->question . '</span>' .
                                (isset($questionsSecond[$i]) ? ' | ' . $questionsSecond[$i]->question : '') . $img;
                            $questionAccTop[$key]['option_a'] = '<span class="notranslate">' . $getQuestions->option_a . '</span>' .
                                (isset($questionsSecond[$i]) ? ' | ' . $questionsSecond[$i]->option_a : '');
                            $questionAccTop[$key]['option_b'] = '<span class="notranslate">' . $getQuestions->option_b . '</span>' .
                                (isset($questionsSecond[$i]) ? ' | ' . $questionsSecond[$i]->option_b : '');
                            $questionAccTop[$key]['option_c'] = '<span class="notranslate">' . $getQuestions->option_c . '</span>' .
                                (isset($questionsSecond[$i]) ? ' | ' . $questionsSecond[$i]->option_c : '');
                            $questionAccTop[$key]['option_d'] = '<span class="notranslate">' . $getQuestions->option_d . '</span>' .
                                (isset($questionsSecond[$i]) ? ' | ' . $questionsSecond[$i]->option_d : '');
                            $questionAccTop[$key]['answer']   = $getQuestions->answer;
    
                            $questionAccTop[$key]['notes'] = !empty($getQuestions->notes)
                                ? '<span class="notranslate">' . $getQuestions->notes . '</span>' .
                                ((isset($questionsSecond[$i]->notes) && $questionsSecond[$i]->notes != '') ? ' | ' . $questionsSecond[$i]->notes : '')
                                : ((isset($questionsSecond[$i]->notes) && $questionsSecond[$i]->notes != '') ? $questionsSecond[$i]->notes : '');
    
                            ++$i;
                        }
                     } else {
                        $questionAccTop[$key]['question'] = '<span class="notranslate">' . $getQuestions->question . '</span>' . $img;
                        $questionAccTop[$key]['option_a'] = '<span class="notranslate">' . $getQuestions->option_a . '</span>';
                        $questionAccTop[$key]['option_b'] = '<span class="notranslate">' . $getQuestions->option_b . '</span>';
                        $questionAccTop[$key]['option_c'] = '<span class="notranslate">' . $getQuestions->option_c . '</span>';
                        $questionAccTop[$key]['option_d'] = '<span class="notranslate">' . $getQuestions->option_d . '</span>';
                        $questionAccTop[$key]['answer']   = $getQuestions->answer;

                        $questionAccTop[$key]['notes'] = !empty($getQuestions->notes)
                            ? '<span class="notranslate">' . $getQuestions->notes . '</span>' : '';
                    }
                }

                $jsonResponse[$languageName][$categoryName][$subcategoryName][$subjectName][$topicsName] = $questionAccTop;
            }

            return response()->json($jsonResponse);
        // } else {
        //     return response()->json(['error' => 'Session ID does not Matched'], 401);
        // }
    }

    function getFirstDropdownData($data, $course)
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

        if ($course) {
            $query->limit($course->question_limit);
        }

        $questions = $query->with(['subCategory',  'subject', 'topic'])->get();

        $language = Language::find($languageId);

        $categories = isset($categoryId) ? Category::where('id', $categoryId)->get() : Category::where('language_id', $languageId)->get();

        $subcategories = isset($data['SubCategory']) ? SubCategory::where('id', $data['SubCategory'])->get() : SubCategory::where('category_id', $categoryId)->get();

        // Get all the subjects for the subcategories
        // $subjects = Subject::whereIn('sub_category_id', $subcategories->pluck('id')->toArray())->get();

        $subjects = isset($data['Subject']) ? Subject::where('id', $data['Subject'])->get() : Subject::whereIn('sub_category_id', $subcategories->pluck('id')->toArray())->get();

        // Get all the topics for the subjects
        $topics = isset($data['Topic']) ? Topic::where('id', $data['Topic'])->get() : Topic::where('subject_id', $subjects->pluck('id')->toArray()[0])->get();

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

        $subjects = isset($data['Subject_2']) ? Subject::where('id', $data['Subject_2'])->get() : Subject::whereIn('sub_category_id', $subcategories->pluck('id')->toArray())->get();
        // Get all the subjects for the subcategories
        // $subjects = Subject::whereIn('sub_category_id', $subcategories->pluck('id')->toArray())->get();

        // Get all the topics for the subjects
        $topics = isset($data['Topic_2']) ? Topic::where('id', $data['Topic_2'])->get() : Topic::whereIn('subject_id', $subjects->pluck('id')->toArray())->get();

        if ($questions->isEmpty()) {
            return null;
        }

        return ['language' => $language, 'categories' => $categories, 'subcategories' => $subcategories, 'subjects' => $subjects, 'topics' => $topics, 'questions' => $questions];
    }
}
