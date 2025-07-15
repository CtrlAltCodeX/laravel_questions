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
use App\Models\UserSession;
use App\Models\Course;
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

        $dropdown_list = [
            'Select Language' => $languages,
            'Select Category' => [],
            'Select SubCategory' => [],
        ];

        return view("cbt.index", compact('languages', 'categories', 'dropdown_list'));
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

    public function getQuestionsData($language_id, $category_id, $subcategory_id, $language2_id = null, $category2_id = null, $subcategory2_id = null)
    {
        $subjects1 = Subject::where('sub_category_id', $subcategory_id)
            ->get();

        $subjects2 = [];

        foreach ($subjects1 as $subject) {
            # code...
            $questions = Question::where('subject_id', $subject->id)
                ->where('language_id', $language_id)
                ->where('category_id',  $category_id)
                ->get()
                ->toArray();

            $questions = count($questions);

            $subject->questions = $questions;

            $subjects2[] = Subject::where('parent_id', $subject->id)->first()?->toArray()??[];
        }

        return response()->json(['subjects1' => $subjects1, 'subjects2' => $subjects2]);
    }

    public function deploy(Request $request)
    {
        if (!$request->header('Authorization')) return response()->json(['error' => 'Please Provide Session Id'], 400);

        if (UserSession::where('session_id', explode(" ", $request->header('Authorization'))[1])->first()) {
            $data = $request->all();

            $questionsFirst = $this->getFirstDropdownData($data) ? $this->getFirstDropdownData($data)['questions'] : [];
            $questionsSecond = $this->getSecondDropdownData($data, $questionsFirst) ? $this->getSecondDropdownData($data, $questionsFirst)['questions'] : null;

            $language = $this->getFirstDropdownData($data)['language'];
            $categories = $this->getFirstDropdownData($data)['categories'][0];
            $subcategories = $this->getFirstDropdownData($data)['subcategories'][0];
            $subjects = $this->getFirstDropdownData($data)['subjects'][0];
            $topics = $this->getFirstDropdownData($data)['topics'];

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
            $questionArray = [];
            $questionAccTop = [];

            foreach ($questionsFirst as $innerKey => $question) {
                $questionArray[] = $question;
            }

            foreach ($questionArray as $key => $getQuestions) {
                $img = isset($getQuestions->photo) && $getQuestions->photo != 0
                    ? '<br><img src="https://iti.online2study.in/storage/questions/' . $getQuestions->photo . '"/>'
                    : (isset($getQuestions->photo_link)
                        ? '<br><img src="' . $getQuestions->photo_link . '"/>'
                        : '');

                $questionAccTop[$key]['question'] = '<span class="notranslate">' . $getQuestions->question . '</span>' .
                    (isset($questionsSecond[$i]) ? ' <br> ' . $questionsSecond[$i]->question : '') . $img;
                $questionAccTop[$key]['option_a'] = '<span class="notranslate">' . $getQuestions->option_a . '</span>' .
                    (isset($questionsSecond[$i]) ? ' <br> ' . $questionsSecond[$i]->option_a : '');
                $questionAccTop[$key]['option_b'] = '<span class="notranslate">' . $getQuestions->option_b . '</span>' .
                    (isset($questionsSecond[$i]) ? ' <br> ' . $questionsSecond[$i]->option_b : '');
                $questionAccTop[$key]['option_c'] = '<span class="notranslate">' . $getQuestions->option_c . '</span>' .
                    (isset($questionsSecond[$i]) ? ' <br> ' . $questionsSecond[$i]->option_c : '');
                $questionAccTop[$key]['option_d'] = '<span class="notranslate">' . $getQuestions->option_d . '</span>' .
                    (isset($questionsSecond[$i]) ? ' <br> ' . $questionsSecond[$i]->option_d : '');
                $questionAccTop[$key]['answer']   = $getQuestions->answer;

                $questionAccTop[$key]['notes'] = !empty($getQuestions->notes)
                    ? '<span class="notranslate">' . $getQuestions->notes . '</span>' .
                    ((isset($questionsSecond[$i]->notes) && $questionsSecond[$i]->notes != '') ? ' <br> ' . $questionsSecond[$i]->notes : '')
                    : ((isset($questionsSecond[$i]->notes) && $questionsSecond[$i]->notes != '') ? $questionsSecond[$i]->notes : '');

                ++$i;
            }

            $jsonResponse[$languageName][$categoryName][$subcategoryName][$subjectName] = $questionAccTop;

            return response()->json($jsonResponse);
        } else {
            return response()->json(['error' => 'Session ID does not Matched'], 401);
        }
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

        $cbtSett = Course::where('language_ids', $languageId)
            ->where('category_id', $categoryId)
            ->where('sub_category_id', "LIKE", "%".$data['SubCategory']."%")
            ->first();

        if ($cbtSett) {
            $query->limit($cbtSett->question_limit);
        }

        $questions = $query->with(['subCategory',  'subject'])->inRandomOrder()
        // ->limit($data['Limit'])
            ->get();

        $language = Language::find($languageId);

        $categories = isset($categoryId) ? Category::where('id', $categoryId)->get() : Category::where('language_id', $languageId)->get();

        $subcategories = isset($data['SubCategory']) ? SubCategory::where('id', $data['SubCategory'])->get() : SubCategory::where('category_id', $categoryId)->get();

        // Get all the subjects for the subcategories
        $subjects = Subject::whereIn('sub_category_id', $subcategories->pluck('id')->toArray())->get();

        // Get all the topics for the subjects
        $topics = Topic::whereIn('subject_id', $subjects->pluck('id')->toArray())->get();

        return ['language' => $language, 'categories' => $categories, 'subcategories' => $subcategories, 'subjects' => $subjects, 'topics' => $topics, 'questions' => $questions];
    }

    function getSecondDropdownData($data, $questions = [])
    {
        $languageId = $data['Language_2'] ?? null;

        $categoryId = $data['Category_2'] ?? null;

        if (!$categoryId) {
            return null;
        }

        $secondLanguageQuestions = [];
        if (count($questions)) {
            foreach ($questions as $question) {
                $getSpecificQuestion = Question::where('question_number', $question->question_number)
                    ->where('language_id', $data['Language_2'])
                    ->where('sub_category_id', $data['SubCategory_2'])
                    ->where('subject_id', $data['Subject_2'])
                    ->first();

                $secondLanguageQuestions[] = $getSpecificQuestion;
            }
        }

        $language = Language::find($languageId);

        $categories = isset($categoryId) ? Category::where('id', $categoryId)->get() : Category::where('language_id', $languageId)->get();

        $subcategories = isset($data['SubCategory_2']) ? SubCategory::where('id', $data['SubCategory_2'])->get() : SubCategory::where('category_id', $categoryId)->get();

        // Get all the subjects for the subcategories
        $subjects = Subject::whereIn('sub_category_id', $subcategories->pluck('id')->toArray())->get();

        return [
            'language' => $language,
            'categories' => $categories,
            'subcategories' => $subcategories,
            'subjects' => $subjects,
            'questions' => $secondLanguageQuestions
        ];
    }
}
