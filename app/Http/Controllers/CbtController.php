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

            $subjects2[] = Subject::where('parent_id', $subject->id)->first()?->toArray() ?? [];
        }

        return response()->json(['subjects1' => $subjects1, 'subjects2' => $subjects2]);
    }

    public function deploy(Request $request, $userId, $courseId)
    {
        //if (!$request->header('Authorization')) return response()->json(['error' => 'Please Provide Session Id'], 400);

        //if (UserSession::where('session_id', explode(" ", $request->header('Authorization'))[1])->first()) {
        $data = $request->all();
        if (!$course = Course::find($courseId)) {
            return response()->json(['error' => 'Course not found'], 404);
        }

        if ($course->language) {
            $categoryId = $course->category_id;
            $category = Category::find($categoryId);
            $subcategory = SubCategory::find($data['SubCategory']);
            // $subject = Subject::find($data['Subject']);

            $data['Language_2'] = $course->language_id;
            $data['Category_2'] = $categoryId;
            $data['SubCategory_2'] = $data['SubCategory'];

            $data['Language'] = 1;
            $data['Category'] = $category->parent_id;
            $data['SubCategory'] = $subcategory->parent_id;
            $response = json_decode($this->getQuestionsData(
                $data['Language'],
                $data['Category'],
                $data['SubCategory'],
                $data['Language_2'],
                $data['Category_2'],
                $data['SubCategory_2']
            )->getContent(), true); // decode as associative array

            $subjectIds2 = [];
            foreach ($response['subjects2'] as $index => $subject) {
                $subjectIds2[$index][] = $subject['id'];
                $subjectIds2[$index][] = $subject['name'];
            }

            $subjectIds1 = [];
            foreach ($response['subjects1'] as $index => $subject) {
                $subjectIds1[$index][] = $subject['id'];
                $subjectIds1[$index][] = $subject['name'];
            }

            $data['Subject_2'] = $subjectIds2;
            $data['Subject'] = $subjectIds1;
            //$data['Topic_2'] = 1;
        } else {
            $data['Language'] = $course->language_id;
            $data['Category'] = $course->category_id;

            $response = json_decode($this->getQuestionsData(
                $data['Language'],
                $data['Category'],
                $data['SubCategory']
            )->getContent(), true); // decode as associative array

            $subjectIds1 = [];
            foreach ($response['subjects1'] as $index => $subject) {
                $subjectIds1[$index][] = $subject['id'];
                $subjectIds1[$index][] = $subject['name'];
            }

            $data['Subject'] = $subjectIds1;
        }

        $language = $this->getFirstDropdownData($data, $course)['language'] ?? null;
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

        if (!$course->language) {
            if (!$course->subject_limit) {
                $arr1 = $course->part_limit['position'];
            } else {
                $arr1 = $course->subject_limit['position'];
            }

            asort($arr1);

            $keys1 = array_keys($arr1);

            $subjectIds1 = $this->reorderArrayByPositions($subjectIds1, $keys1);

            $i = 0;
            foreach ($subjectIds1 as $key => $subject) {
                if (!in_array($subject[0], $course->subject_id)) continue;

                $subjectName = '<span class="notranslate">' . $subject[1] . '</span>';
                if (count($subjects2)) {
                    $subjectName .= ' | ' . $subjectIds2[$key][1];
                }

                $questionArray = [];
                $questionAccTop = [];
                $data['Subject'] = $subject[0];

                if ($course->language) {
                    $data['Subject_2'] = $subjectIds2[$key][0];
                }

                $questionsFirst = $this->getFirstDropdownData($data, $course) ? $this->getFirstDropdownData($data, $course)['questions'] : [];
                $questionsSecond = $this->getSecondDropdownData($data, $questionsFirst) ? $this->getSecondDropdownData($data, $questionsFirst)['questions'] : null;

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

                if ($course->part_limit) {
                    $subjectsWithPartB = [];
                    $subjectsWithPartA = [];
                    foreach ($course->part_limit['limit'] as $subjectId => $limit) {
                        if ($subjectId == $subject[0]) {
                            if ($limit[0]) {
                                $subjectsWithPartB[] = '<span class="notranslate">' . $subject[1] . '</span>';
                            } else if ($limit[1]) {
                                $subjectsWithPartA[] = '<span class="notranslate">' . $subject[1] . '</span>';
                            }
                        }
                    }

                    if (in_array($subjectName, $subjectsWithPartA) && $course->part_limit['limit']) {
                        $jsonResponse[$languageName][$categoryName][$subcategoryName]['Part B'][$subjectName] = $questionAccTop;
                    }

                    if (in_array($subjectName, $subjectsWithPartB)) {
                        $jsonResponse[$languageName][$categoryName][$subcategoryName]['Part A'][$subjectName] = $questionAccTop;
                    }
                } else if ($course->subject_limit) {
                    $jsonResponse[$languageName][$categoryName][$subcategoryName][$subjectName] = $questionAccTop;
                }
            }
        } else {
            if (!$course->subject_limit) {
                $arr1 = $course->part_limit['position'];
            } else {
                $arr1 = $course->subject_limit['position'];
            }

            $arr2 = [];
            foreach ($arr1 as $key => $value) {
                $subjectParentId = Subject::find($key);
                if ($subjectParentId) {
                    $arr2[$subjectParentId->parent_id] = $value;
                }
            }

            asort($arr1);
            asort($arr2);

            $keys1 = array_keys($arr1);
            $keys2 = array_keys($arr2);

            $subjects2 = $this->reorderArrayByPositions($subjectIds2, $keys1);
            $subjectIds1 = $this->reorderArrayByPositions($subjectIds1, $keys2);

            $i = 0;
            foreach ($subjects2 as $key => $subject) {
                if (!in_array($subject[0], $course->subject_id)) continue;

                $subjectName = '<span class="notranslate">' . $subjectIds1[$key][1] . '</span>';
                if (count($subjects2)) {
                    $subjectName .= ' | ' . $subject[1];
                }

                $questionArray = [];
                $questionAccTop = [];
                $data['Subject'] = $subjectIds1[$key][0];

                if ($course->language) {
                    $data['Subject_2'] = $subject[0];
                }

                $questionsFirst = $this->getFirstDropdownData($data, $course) ? $this->getFirstDropdownData($data, $course)['questions'] : [];
                $questionsSecond = $this->getSecondDropdownData($data, $questionsFirst) ? $this->getSecondDropdownData($data, $questionsFirst)['questions'] : null;

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
                        (isset($questionsSecond[$key]) ? ' <br> ' . $questionsSecond[$key]->question : '') . $img;
                    $questionAccTop[$key]['option_a'] = '<span class="notranslate">' . $getQuestions->option_a . '</span>' .
                        (isset($questionsSecond[$key]) ? ' <br> ' . $questionsSecond[$key]->option_a : '');
                    $questionAccTop[$key]['option_b'] = '<span class="notranslate">' . $getQuestions->option_b . '</span>' .
                        (isset($questionsSecond[$key]) ? ' <br> ' . $questionsSecond[$key]->option_b : '');
                    $questionAccTop[$key]['option_c'] = '<span class="notranslate">' . $getQuestions->option_c . '</span>' .
                        (isset($questionsSecond[$key]) ? ' <br> ' . $questionsSecond[$key]->option_c : '');
                    $questionAccTop[$key]['option_d'] = '<span class="notranslate">' . $getQuestions->option_d . '</span>' .
                        (isset($questionsSecond[$key]) ? ' <br> ' . $questionsSecond[$key]->option_d : '');
                    $questionAccTop[$key]['answer']   = $getQuestions->answer;

                    $questionAccTop[$key]['notes'] = !empty($getQuestions->notes)
                        ? '<span class="notranslate">' . $getQuestions->notes . '</span>' .
                        ((isset($questionsSecond[$i]->notes) && $questionsSecond[$i]->notes != '') ? ' <br> ' . $questionsSecond[$i]->notes : '')
                        : ((isset($questionsSecond[$i]->notes) && $questionsSecond[$i]->notes != '') ? $questionsSecond[$i]->notes : '');

                    ++$i;
                }

                if (isset($course->part_limit['limit'])) {
                    $subjectsWithPartB = [];
                    $subjectsWithPartA = [];
                    foreach ($course->part_limit['limit'] as $subjectId => $limit) {
                        if ($subject[0] == $subjectId) {
                            if ($limit[0]) {
                                $subjectsWithPartB[] = '<span class="notranslate">' . $subject[1] . '</span>';
                            } else if ($limit[1]) {
                                $subjectsWithPartA[] = '<span class="notranslate">' . $subject[1] . '</span>';
                            }
                        }
                    }

                    $subjectNameData = '<span class="notranslate">' . $subject[1] . '</span>';

                    if (in_array($subjectNameData, $subjectsWithPartA)) {
                        $jsonResponse[$languageName][$categoryName][$subcategoryName]['Part B'][$subjectName] = $questionAccTop;
                    }

                    if (in_array($subjectNameData, $subjectsWithPartB) && $course->part_limit['limit']) {
                        $jsonResponse[$languageName][$categoryName][$subcategoryName]['Part A'][$subjectName] = $questionAccTop;
                    }
                } else if ($course->subject_limit) {
                    $jsonResponse[$languageName][$categoryName][$subcategoryName][$subjectName] = $questionAccTop;
                }
            }
        }

        return response()->json($jsonResponse);
        //} else {
        //  return response()->json(['error' => 'Session ID does not Matched'], 401);
        //}
    }

    function reorderArrayByPositions(array $data, array $positions): array
    {
        $reordered = [];

        foreach ($positions as $id) {
            foreach ($data as $item) {
                if ($item[0] == $id) {
                    $reordered[] = $item;
                    break;
                }
            }
        }

        return $reordered;
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

        $subject = $data['Subject'];
        if ($course->language) {
            $subject = $data['Subject_2'];
        }

        $skip = false;
        if (gettype($data['Subject']) != 'array') {
            if ($course->subject_limit) {
                if (!$course->subject_limit[$subject]) {
                    $skip = true;
                }

                foreach ($course->subject_limit as $subjectId => $limit) {
                    if ($subject == $subjectId) {
                        $query->limit($limit);
                    }
                }
            } else if ($course->part_limit) {
                foreach ($course->part_limit['limit'] as $subjectId => $limit) {
                    if ($subject == $subjectId) {
                        if ($limit[0]) {
                            $query->limit($limit[0]);
                        } else if ($limit[1]) {
                            $query->limit($limit[1]);
                        }
                    }
                }
            }
        }

        $questions = $query->with(['subCategory',  'subject'])->inRandomOrder()
            ->get();

        if ($skip) $questions = [];

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
