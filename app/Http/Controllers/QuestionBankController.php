<?php

namespace App\Http\Controllers;

use App\Imports\QuestionsImport;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Exports\QuestionsExport;
use App\Models\Language;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\UserSession;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Course;

class QuestionBankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $languages = Language::all();
        $categories = [];
        $subcategories = [];
        $subjects = [];
        $topics = [];
        $query = Question::query();

        // Initialize filter variables
        $language_id = request()->language_id;
        $category_id = request()->category_id;
        $sub_category_id = request()->sub_category_id;
        $subject_id = request()->subject_id;
        $topic_id = request()->topic_id;
        $search = request()->search; // Search parameter

        // Apply filters if they exist
        if ($language_id) {
            $categories = Category::where('language_id', $language_id)->get();
            $query->where('language_id', $language_id);
        }

        if ($category_id) {
            $subcategories = SubCategory::where('category_id', $category_id)->get();
            $query->where('category_id', $category_id);
        }

        if ($sub_category_id) {
            $subjects = Subject::where('sub_category_id', $sub_category_id)->get();
            $query->where('sub_category_id', $sub_category_id);
        }

        if ($subject_id) {
            $topics = Topic::where('subject_id', $subject_id)->get();
            $query->where('subject_id', $subject_id);
        }

        if ($topic_id) {
            $query->where('topic_id', $topic_id);
        }

        // Apply search filter to question text or options
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('question', 'LIKE', "%{$search}%")
                    ->orWhere('option_a', 'LIKE', "%{$search}%")
                    ->orWhere('option_b', 'LIKE', "%{$search}%")
                    ->orWhere('option_c', 'LIKE', "%{$search}%")
                    ->orWhere('option_d', 'LIKE', "%{$search}%");
            });
        }

        // Eager load relationships
        $query->with(['category', 'subCategory', 'subject', 'topic', 'language']);

        // Handle sorting
        $sortColumn = request()->get('sort', 'id');
        $sortDirection = request()->get('direction', 'asc');

        if ($sortColumn == 'language.name') {
            $query = $query
                ->join('languages', 'questions.language_id', '=', 'languages.id')
                ->orderBy('languages.name', $sortDirection)
                ->select('questions.*');
        } elseif ($sortColumn == 'question_number') {
            $query = $query->orderBy('question_number', $sortDirection);
        } else {
            $query = $query->orderBy($sortColumn, $sortDirection);
        }

        // Paginate results
        $questions = $query->paginate(request()->get('per_page', 10));

        $dropdown_list = [
            'Select Language' => $languages,
            'Select Category' => $categories,
            'Select Sub Category' => $subcategories ?? [],
            'Select Subject' => $subjects ?? [],
            'Select Topic' => $topics ?? [],
        ];

        $levels = [
            '1' => 'Easy',
            '2' => 'Medium',
            '3' => 'Hard',
        ];

        return view(
            'question-bank.index',
            compact(
                'questions',
                'language_id',
                'category_id',
                'sub_category_id',
                'subject_id',
                'topic_id',
                'languages',
                'categories',
                'subcategories',
                'subjects',
                'topics',
                'sortColumn',
                'sortDirection',
                'dropdown_list',
                'levels'
            )
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $questions = Question::all();

        $languages = Language::all();

        $categories = Category::all();

        $subCategories = SubCategory::all();

        $subjects = Subject::all();

        $topics = Topic::all();

        return view('question-bank.create', compact('questions', 'languages', 'categories', 'subCategories', 'subjects', 'topics'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [];

        foreach ($request->input('module') as $moduleKey => $moduleValues) {
            $rules['module.' . $moduleKey] = 'required|array|min:1';
        }

        $request->validate($rules);

        $request->validate([
            'question' => 'required',
            'option_a' => 'required',
            'option_b' => 'required',
            'option_c' => 'required',
            'option_d' => 'required',
            'answer' => 'required',
        ], [
            'question.required' => 'The question field is required.',
            'option_a.required' => 'The option a field is required.',
            'option_b.required' => 'The option b field is required.',
            'option_c.required' => 'The option c field is required.',
            'option_d.required' => 'The option d field is required.',
            'answer.required' => 'The answer field is required.',
        ]);

        $data = $request->all();

        $profileImage = null;
        if ($file = $data['photo']) {
            if ($file instanceof UploadedFile) {
                $profileImage = time() . "." . $file->getClientOriginalExtension();
                $file->move('storage/questions/', $profileImage);
                // Update the hidden input value with the new file path
                $data['photo'] = $profileImage;
            } else {
                !empty($file) ? $profileImage = $file : $profileImage = null;
            }
        }

        // dd($data);die;

        $question = Question::create(
            [
                'question' => $data['question'][0],
                'photo' => $data['photo'],
                'question_number' => $data['qno'],
                'photo_link' => $data['photo_link'],
                'notes' => $data['notes'][0],
                'level' => $data['level'],
                'option_a' => $data['option_a'][0],
                'option_b' => $data['option_b'][0],
                'option_c' => $data['option_c'][0],
                'option_d' => $data['option_d'][0],
                'answer' => $data['answer'][0],
                'language_id' => $data['language'][0],
                'category_id' => $data['module']['Category'][0],
                'sub_category_id' => $data['module']['Sub Category'][0],
                'subject_id' => $data['module']['Subject'][0],
                'topic_id' => $data['module']['Topic'][0],
            ]
        );

        // session()->flash('success', 'Question created successfully!');

        // return redirect()->route('question.index');
        return response()->json(['success' => 'Question created successfully!']);
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
        $question = Question::where('id', $id)->with('question_bank')->first();
        if (!$question) {
            return response()->json(['error' => 'Question not found.'], 404);
        }

        $languages = Language::all();
        $categories = Category::where('language_id', $question->language_id)->get();
        $subCategories = SubCategory::where('category_id', $question->category_id)->get();
        $subjects = Subject::where('sub_category_id', $question->sub_category_id)->get();
        $topics = Topic::where('subject_id', $question->subject_id)->get();

        $dropdown_list = [
            'Select Language' => $languages,
            'Select Category' => $categories,
            'Select Sub Category' => $subCategories,
            'Select Subject' => $subjects,
            'Select Topic' => $topics,
        ];


        return response()->json([
            'success' => true,
            'question' => $question,
            'languages' => $languages, // Include languages
            'dropdown_list' => $dropdown_list,
            'categories' => $categories,
            'subCategories' => $subCategories,
            'subjects' => $subjects,
            'topics' => $topics,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // dd($request->all());

        $rules = [];

        foreach ($request->input('module') as $moduleKey => $moduleValues) {
            $rules['module.' . $moduleKey] = 'required|array|min:1';
        }

        $request->validate($rules);

        $request->validate([
            'question' => 'required',
            'option_a' => 'required',
            'option_b' => 'required',
            'option_c' => 'required',
            'option_d' => 'required',
            'answer' => 'required',
        ], [
            'question.required' => 'The question field is required.',
            'option_a.required' => 'The option a field is required.',
            'option_b.required' => 'The option b field is required.',
            'option_c.required' => 'The option c field is required.',
            'option_d.required' => 'The option d field is required.',
            'answer.required' => 'The answer field is required.',
        ]);

        $data = $request->all();

        $question = Question::findOrFail($data['id']);

        $profileImage = null;
        if ($file = $data['photo']) {
            if ($file instanceof UploadedFile) {
                $profileImage = time() . "." . $file->getClientOriginalExtension();
                $file->move('storage/questions/', $profileImage);
                $data['photo'] = $profileImage;
            } else {
                !empty($file) ? $profileImage = $file : $profileImage = null;
            }
        }

        Question::updateOrCreate(
            [
                'id' => $data['id'],
            ],
            [
                'id' => $data['id'],
                'question_number' => $data['qno'][0],
                'question' => $data['question'][0],
                'photo' => ($data['photo'] != 'null') ? $data['photo'] : null,
                'photo_link' => $data['photo_link'] ?? null,
                'notes' => $data['notes'][0],
                'level' => $data['level'],
                'option_a' => $data['option_a'],
                'option_b' => $data['option_b'],
                'option_c' => $data['option_c'],
                'option_d' => $data['option_d'],
                'answer' => $data['answer'],
                'language_id' => $data['module']['select_language'][0],
                'category_id' => $data['module']['select_category'][0],
                'sub_category_id' => $data['module']['select_sub_category'][0],
                'subject_id' => $data['module']['select_subject'][0],
                'topic_id' => $data['module']['select_topic'][0],
                'question_bank_id' => null
            ]
        );

        return response()->json(['success' => true, 'message' => 'Question updated successfully!',]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroyQuestion(string $id)
    {
        $question = Question::findOrFail($id);
        if (isset($question)) {

            $question->delete();
        }

        session()->flash('success', 'Question deleted successfully!');
    }

    public function destroy(string $id)
    {
        // $translateQuestion = TranslatedQuestions::where('question_id', $id);
        $question = Question::find($id);

        // $translateQuestion->delete();
        $question->delete();

        return redirect()->route('question.index');
    }

    public function export(Request $request)
    {
        $languages = $request->input('languages', []);
        $query = Question::query();

        if ($request->language_id != '') {
            $query->where('language_id', $request->language_id);
        }

        if ($request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        if ($request->sub_category_id != '') {
            $query->where('sub_category_id', $request->sub_category_id);
        }

        if ($request->subject_id != '') {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->topic_id != '') {
            $query->where('topic_id', $request->topic_id);
        }

        // Get the questions based on filters
        $question_query = $query->with(['topic', 'subject', 'subCategory', 'category'])->get();

        $questions = [];

        foreach ($question_query as $question) {
            $questionId = $question->id;

            if (!isset($questions[$questionId])) {
                $questions[$questionId] = [
                    'question' => $question->question,  // Assuming question_text is part of the questions table
                    'option_a' => $question->option_a,
                    'option_b' => $question->option_b,
                    'option_c' => $question->option_c,
                    'option_d' => $question->option_d,
                    'answer' => $question->answer,
                    'level' => $question->level,
                    'photo' => isset(explode("/", $question->photo)[2]) ? explode("/", $question->photo)[2] : $question->photo,
                    'photo_link' => $question->photo_link,
                    'category' => $question->category_id ?? '',
                    'subCategory' => $question->sub_category_id ?? '',
                    'subject' => $question->subject_id ?? '',
                    'topic' => $question->topic_id ?? '',
                    'qno' => $question->question_number,
                    'notes' => $question->notes,
                    'id' => $questionId,
                    'language_id' => $question->language_id,
                ];
            }

            // $questions[$questionId]['language'][$question->language_id] = $translatedQuestion->language->name;
            // $questions[$questionId]['question'][$question->language_id] = $translatedQuestion->question_text;
            // $questions[$questionId]['option_a'][$question->language_id] = $translatedQuestion->option_a;
            // $questions[$questionId]['option_b'][$question->language_id] = $translatedQuestion->option_b;
            // $questions[$questionId]['option_c'][$question->language_id] = $translatedQuestion->option_c;
            // $questions[$questionId]['option_d'][$question->language_id] = $translatedQuestion->option_d;
        }

        // Export the questions as an Excel file
        return Excel::download(new QuestionsExport($questions, $languages), 'questions.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);

        // Load the file to check rows before importing
        $rows = Excel::toArray(new QuestionsImport, $request->file('file'));
        $headings = array_keys($rows[0][0]);
        $substring = "language";
        $languageCount = 0;
        $languageIds = [];

        foreach ($headings as $key) {
            if (strpos($key, $substring) !== false) {
                $languageCount++;
                $id = str_replace($substring . '_', '', $key);
                $languageIds[] = $id;
            }
        }

        foreach ($rows[0] as $key => $row) {
            $rowCount = $key + 2;
            $requiredKeys = ['id', 'qno', 'language_id', 'category', 'subcategory', 'subject', 'topic', 'question', 'option_a', 'option_b', 'option_c', 'option_d', 'answer', 'notes', 'level', 'photo', 'photo_link'];

            // Check if all required keys exist in the row
            foreach ($requiredKeys as $requiredKey) {
                if (!array_key_exists($requiredKey, $row)) {
                    return back()->with('error', 'The key "' . $requiredKey . '" is missing in the data row.');
                }
            }

            if (empty($row['qno'])) {
                return back()->with('error', 'Row: ' . $rowCount . '- Question No. is required.');
            }

            if (empty($row['question'])) {
                return back()->with('error', 'Row: ' . $rowCount . '- Question is required.');
            }

            if (empty($row['option_a'])) {
                return back()->with('error', 'Row: ' . $rowCount . '- Option A is required.');
            }

            if (empty($row['option_b'])) {
                return back()->with('error', 'Row: ' . $rowCount . '- Option B is required.');
            }

            if (empty($row['option_c'])) {
                return back()->with('error', 'Row: ' . $rowCount . '- Option C is required.');
            }

            if (empty($row['option_d'])) {
                return back()->with('error', 'Row: ' . $rowCount . '- Option D is required.');
            }

            if (empty($row['answer'])) {
                return back()->with('error', 'Row: ' . $rowCount . '- Answer is required.');
            }

            if (!in_array($row['answer'], ['A', 'B', 'C', 'D'])) {
                return back()->with('error', 'Row: ' . $rowCount . ' - Answer must be A, B, C, or D.');
            }

            if (empty($row['language_id'])) {
                return back()->with('error', 'Row: ' . $rowCount . '- language is required.');
            }

            if (!$this->getLanguageId($row['language_id'])) {
                return back()->with('error', 'Language -  "' . $row['language_id'] . '" not available');
            }

            if (empty($row['category'])) {
                return back()->with('error', 'Row: ' . $rowCount . '- Category is required.');
            }

            if (!$this->getCategoryId($row['category'])) {
                return back()->with('error', 'Category - "' . $row['category'] . '" not available');
            }

            if (empty($row['subcategory'])) {
                return back()->with('error', 'Row: ' . $rowCount . '- Subcategory is required.');
            }
            if (!$this->getSubCategoryId($row['subcategory'])) {
                return back()->with('error', 'Subcategory - "' . $row['subcategory'] . '" not available');
            }

            if (empty($row['subject'])) {
                return back()->with('error', 'Row: ' . $rowCount . '- Subject is required.');
            }
            if (!$this->getSubjectId($row['subject'])) {
                return back()->with('error', 'Subject - "' . $row['subject'] . '" not available');
            }

            if (empty($row['topic'])) {
                return back()->with('error', 'Row: ' . $rowCount . '- Topic is required.');
            }
            if (!$this->getTopicId($row['topic'])) {
                return back()->with('error', 'Topic - "' . $row['topic'] . '" not available');
            }
        }

        Excel::import(new QuestionsImport, $request->file('file'));

        return back()->with('success', 'Questions imported successfully.');
    }

    private function getLanguageId($id)
    {
        return \App\Models\Language::find($id)->id ?? null;
    }

    private function getCategoryId($id)
    {
        return \App\Models\Category::find($id)->id ?? null;
    }

    private function getSubCategoryId($id)
    {
        return \App\Models\SubCategory::find($id)->id ?? null;
    }

    private function getSubjectId($id)
    {
        return \App\Models\Subject::find($id)->id ?? null;
    }

    private function getTopicId($id)
    {
        return \App\Models\Topic::find($id)->id ?? null;
    }

    public function getCategories($languageId)
    {
        $categories = Category::where('language_id', $languageId)->get();

        return response()->json($categories);
    }

    public function getSubCategories($categoryId)
    {
        $subCategories = SubCategory::where('category_id', $categoryId)->get();

        return response()->json($subCategories);
    }

    public function getSubjects($subCategoryId)
    {
        $subCategoryIds = explode(',', $subCategoryId);

        $subjects = Subject::whereIn('sub_category_id', $subCategoryIds)->get();

        return response()->json($subjects);
    }

    public function getTopics($subjectId)
    {
        $topics = Topic::where('subject_id', $subjectId)->get();

        return response()->json($topics);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids');

        if (!empty($ids)) {
            Question::whereIn('id', $ids)
                ->delete();

            return response()->json(['message' => 'Selected questions deleted successfully.']);
        }

        return response()->json(['message' => 'No questions selected.'], 400);
    }

    public function questionNoExist()
    {
        if (!request()->exist) {
            $questionNo = Question::where('category_id', request()->category_id)
                ->where('sub_category_id', request()->sub_category_id)
                ->where('subject_id', request()->subject_id)
                ->where('topic_id', request()->topic_id)
                ->max('question_number');

            return $questionNo;
        } else {
            $questionNoExist = Question::where('category_id', request()->category_id)
                ->where('sub_category_id', request()->sub_category_id)
                ->where('subject_id', request()->subject_id)
                ->where('topic_id', request()->topic_id)
                ->where('question_number', request()->q_no)
                ->count();

            return $questionNoExist;
        }
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
                $subject = Subject::find($data['Subject']);

                $data['Language_2'] = $course->language_id;
                $data['Category_2'] = $categoryId;
                $data['SubCategory_2'] = $data['SubCategory'];
                $data['Subject_2'] = $data['Subject'];

                $data['Language'] = 1;
                $data['Category'] = $category->parent_id;
                $data['SubCategory'] = $subcategory->parent_id;
                $data['Subject'] = $subject->parent_id;
                // $data['Topic_2'] = 1;
            } else {
                $data['Language'] = $course->language_id;
                $data['Category'] = $course->category_id;
            }
      
            //$questionsFirst = $this->getFirstDropdownData($data, $course) ? $this->getFirstDropdownData($data, $course)['questions'] : [];
            //$questionsSecond = $this->getSecondDropdownData($data) ? $this->getSecondDropdownData($data)['questions'] : null;

            $language = $this->getFirstDropdownData($data, $course)['language'];
            $categories = $this->getFirstDropdownData($data, $course)['categories'];
            $subcategories = $this->getFirstDropdownData($data, $course)['subcategories'];
            $subjects = $this->getFirstDropdownData($data, $course)['subjects'];
            $topics = $this->getFirstDropdownData($data, $course)['topics'];

            $language2 = $this->getSecondDropdownData($data)['language'] ?? null;
            $categories2 = $this->getSecondDropdownData($data)['categories'] ?? collect();
            $subcategories2 = $this->getSecondDropdownData($data)['subcategories'] ?? collect();
            $subjects2 = $this->getSecondDropdownData($data)['subjects'] ?? collect();
            $topics2 = $this->getSecondDropdownData($data)['topics'] ?? collect();

            // Transform the questions into the desired JSON structure
            $jsonResponse = [];

            $languageName = $language->name;
            if ($language2) {
                $languageName .= ' | ' . $language2->name;
            }

            foreach ($categories as $category) {
                $categoryName = $category->name;
                if ($categories2->isNotEmpty()) {
                    foreach ($categories2 as $category2) {
                        $combinedCategoryName = $categoryName . ' | ' . $category2->name;
                    }
                } else {
                    $combinedCategoryName = $categoryName;
                }
              
              
              
                foreach ($topics as $outkey => $topic) {
                    // Filter topics based on the selected topic
                    // if (isset($data['Topic']) && $topic->id != $data['Topic']) {
                    //     continue;
                    // }

                    $subcategoryName = $subcategories->name;
                    $subjectName = $subjects->name;
                    $topicName = $topic->name;

                    if ($subcategories2) {
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
                    
                    $data['Topic'] = $topic->id;
                    $data['Topic_2'] = $topics2[$outkey]->id;
                  	
                  	if ($topics2->isNotEmpty()) {
                        foreach ($topics2 as $topic2) {
                            $combinedTopicName = $topicName . ' | ' . $topics2[$outkey]->name;
                        }
                    } else {
                        $combinedTopicName = $topicName;
                    }
                  
                  
                    $questionsFirst = $this->getFirstDropdownData($data, $course) ? $this->getFirstDropdownData($data, $course)['questions'] : [];
                    $questionsSecond = $this->getSecondDropdownData($data) ? $this->getSecondDropdownData($data)['questions'] : null;

                  	$filteredQuestions = $questionsFirst->filter(function ($question) use ($topic, $topics2) {
                        return $question->topic_id == $topic->id || $topics2->contains('id', $question->topic_id);
                    });


                    // foreach ($filteredQuestions as $questionFirst) {
                    if (isset($questionsSecond)) {
                        foreach ($questionsSecond as $key => $questionSecond) {
                            $jsonResponse[$languageName][$combinedCategoryName][$combinedSubcategoryName][$combinedSubjectName][$data['Topic_2']][$combinedTopicName][] = [
                                'question' => (htmlspecialchars($filteredQuestions[$key]->question)) . ' | ' . htmlspecialchars($questionSecond->question) . (isset($filteredQuestions[$key]->photo) ? '<br>' . '<img src="' . $filteredQuestions[$key]->photo . '"/>' : '<img src="' . $filteredQuestions[$key]->photo_link . '"/>'),
                                'options' => [
                                    (htmlspecialchars($filteredQuestions[$key]->option_a)) . ' | ' . htmlspecialchars($questionSecond->option_a),
                                    (htmlspecialchars($filteredQuestions[$key]->option_b)) . ' | ' . htmlspecialchars($questionSecond->option_b),
                                    (htmlspecialchars($filteredQuestions[$key]->option_c)) . ' | ' . htmlspecialchars($questionSecond->option_c),
                                    (htmlspecialchars($filteredQuestions[$key]->option_d)) . ' | ' . htmlspecialchars($questionSecond->option_d),
                                ],
                                'answer' => $filteredQuestions[$key]->answer,
                            ];
                        }
                    } else {
                        foreach ($filteredQuestions as $questionFirst) {
                            $img = isset($questionFirst->photo) && $questionFirst->photo != 0
                                ? '<br><img src="https://iti.online2study.in/storage/questions/' . $questionFirst->photo . '"/>'
                                : (isset($questionFirst->photo_link)
                                    ? '<br><img src="' . $questionFirst->photo_link . '"/>'
                                    : '');

                            $jsonResponse[$languageName][$combinedCategoryName][$combinedSubcategoryName][$combinedSubjectName][$data['Topic']][$combinedTopicName][] = [
                                'question_id' => $questionFirst->id,
                                'question' => (htmlspecialchars($questionFirst->question)) . $img,
                                'options' => [
                                    htmlspecialchars($questionFirst->option_a),
                                    htmlspecialchars($questionFirst->option_b),
                                    htmlspecialchars($questionFirst->option_c),
                                    htmlspecialchars($questionFirst->option_d),
                                ],
                                'answer' => $questionFirst->answer, // Assuming this field exists
                                'notes' => $questionFirst->notes // Assuming this field exists
                            ];
                        }
                    }
                }
            }

            // Function to recursively remove empty arrays
            return response()->json($jsonResponse);
        //} else {
          //  return response()->json(['error' => 'Session ID does not Matched'], 401);
        //}
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
      
	    //if ($course) {
          //  $query->limit($course->question_limit);
        //}

        $questions = $query->with(['subCategory',  'subject', 'topic'])->get();

        $language = Language::find($languageId);

        $categories = isset($categoryId) ? Category::where('id', $categoryId)->get() : Category::where('language_id', $languageId)->get();

        $subcategories = isset($data['SubCategory']) ? SubCategory::where('id', $data['SubCategory'])->first() : SubCategory::where('category_id', $categoryId)->first();

        // Get all the subjects for the subcategories
//        $subjects = Subject::where('sub_category_id', $subcategories->id)->first();
      
	    $subjects = Subject::find($data['Subject']);

        // Get all the topics for the subjects
        $topics = Topic::where('subject_id', $subjects->id)->get();
      
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
	    //  $subjects = Subject::where('sub_category_id', $subcategories->id)->get();
      
        // Get all the topics for the subjects
        //$topics = isset($data['Topic_2']) ? Topic::where('id', $data['Topic_2'])->get() : Topic::whereIn('subject_id', $subjects->pluck('id')->toArray())->get();
	    $topics = Topic::whereIn('subject_id', $subjects->pluck('id')->toArray())->get();
      
        if ($questions->isEmpty()) {
            return null;
        }

        return ['language' => $language, 'categories' => $categories, 'subcategories' => $subcategories, 'subjects' => $subjects, 'topics' => $topics, 'questions' => $questions];
    }

    function getSubCategoriesFromSubject($subjectId)
    {
        $subject = Subject::with('subCategory')->where('id', $subjectId)->first();

        if (!$subject) {
            return response()->json(['error' => 'No subcategories found for this subject.'], 404);
        }

        return response()->json([$subject, $subject->subCategory]);
    }
}
