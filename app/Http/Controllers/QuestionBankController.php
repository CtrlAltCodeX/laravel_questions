<?php

namespace App\Http\Controllers;

use App\Imports\QuestionsImport;
use App\Models\Question;
use App\Models\TranslatedQuestions;
use Illuminate\Http\Request;
use App\Exports\QuestionsExport;
use App\Models\Language;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;

class QuestionBankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $languages = Language::all();
        $categories = Category::all();
        $subcategories = [];
        $subjects = [];
        $topics = [];
        $query = Question::query();
        $translation_questions_query = TranslatedQuestions::query();
    
        // Initialize filter variables
        $language_id = request()->language_id;
        $category_id = request()->category_id;
        $sub_category_id = request()->sub_category_id;
        $subject_id = request()->subject_id;
        $topic_id = request()->topic_id;
        $search = request()->search; // New search parameter

        // Apply filters if they exist
        if ($language_id) {
            $translation_questions_query->where('translated_questions.language_id', $language_id);
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

        if ($search) {
            $translation_questions_query->where(function ($q) use ($search) {
                $q->where('question_text', 'LIKE', "%{$search}%")
                    ->orWhere('translated_questions.option_a', 'LIKE', "%{$search}%")
                    ->orWhere('translated_questions.option_b', 'LIKE', "%{$search}%")
                    ->orWhere('translated_questions.option_c', 'LIKE', "%{$search}%")
                    ->orWhere('translated_questions.option_d', 'LIKE', "%{$search}%");
            });
        }

        // Eager load relationships
        $query->with(['category', 'subCategory', 'subject', 'topic']);
        $translation_questions_query->with(['language', 'question']);

        // Fetch questions
        $questions = $query->get();

        // Fetch translated questions based on the main questions
        $translatedQuestions = $translation_questions_query
            ->whereIn('question_id', $questions->pluck('id'));

        // Handle sorting
        $sortColumn = request()->get('sort', 'id');
        $sortDirection = request()->get('direction', 'asc');

        if ($sortColumn == 'language.name') {
            $translatedQuestions = $translatedQuestions
                ->join('languages', 'translated_questions.language_id', '=', 'languages.id')
                ->orderBy('languages.name', $sortDirection)
                ->select('translated_questions.*');
        } elseif ($sortColumn == 'question.question_number') { // Sort by question number
            $translatedQuestions = $translatedQuestions
                ->join('questions', 'translated_questions.question_id', '=', 'questions.id')
                ->orderBy('questions.question_number', $sortDirection)
                ->select('translated_questions.*');
        } else {
            $translatedQuestions = $translatedQuestions->orderBy($sortColumn, $sortDirection);
        }

        // Paginate results
        $translatedQuestions = $translatedQuestions->paginate(request()->get('per_page', 10));

        return view(
            'question-bank.index',
            compact(
                'translatedQuestions',
                'language_id',
                'category_id',
                'sub_category_id',
                'subject_id',
                'topic_id',
                'languages',
                'categories',
                'questions',
                'subcategories',
                'subjects',
                'topics',
                'sortColumn',
                'sortDirection'
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
                $data['photo'] = 'storage/questions/' . $profileImage;
            } else {
                !empty($file) ? $profileImage = '/storage/questions/' . $file : $profileImage = null;
            }
        }

        // dd($data);

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
        // dd($question);

        if (count($data['language']) > 0) {
            foreach ($data['language'] as $index => $languageId) {
                TranslatedQuestions::updateOrCreate(
                    [
                        'question_id' => $question->id,
                        'language_id' => $languageId,
                    ],
                    [
                        'question_id' => $question->id,
                        'language_id' => $languageId,
                        'question_text' => $data['question'][$index],
                        'option_a' => $data['option_a'][$index],
                        'option_b' => $data['option_b'][$index],
                        'option_c' => $data['option_c'][$index],
                        'option_d' => $data['option_d'][$index],
                    ]
                );
            }
        }

        session()->flash('success', 'Question created successfully!');

        return redirect()->route('question.index');
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

        $questions = Question::where('question_bank_id', $id)->get();

        $translatedQuestions = TranslatedQuestions::where('question_id', $id)->with('question')->get();

        $translatedQuestions = $translatedQuestions->filter(function ($translatedQuestion) use ($question) {
            return $translatedQuestion->language_id != $question->language_id;
        });


        $languages = Language::all();

        $categories = Category::all();

        $subCategories = SubCategory::all();

        $subjects = Subject::all();

        $topics = Topic::all();

        return view('question-bank.edit', compact('question', 'languages', 'categories', 'subCategories', 'subjects', 'topics', 'questions', 'translatedQuestions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
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

        $question = Question::findOrFail($data['id']);

        if (count($data['language']) > 0) {
            foreach ($data['language'] as $index => $languageId) {
                TranslatedQuestions::updateOrCreate(
                    [
                        'question_id' => $question->id,
                        'language_id' => $languageId,
                    ],
                    [
                        'question_id' => $question->id,
                        'language_id' => $languageId,
                        'question_text' => $data['question'][$index],
                        'option_a' => $data['option_a'][$index],
                        'option_b' => $data['option_b'][$index],
                        'option_c' => $data['option_c'][$index],
                        'option_d' => $data['option_d'][$index],
                    ]
                );
            }
        }

        $profileImage = null;
        if ($file = $data['photo']) {
            if ($file instanceof UploadedFile) {
                $profileImage = time() . "." . $file->getClientOriginalExtension();
                $file->move('storage/questions/', $profileImage);
                $data['photo'] = 'storage/questions/' . $profileImage;
            } else {
                !empty($file) ? $profileImage = '/storage/questions/' . $file : $profileImage = null;
            }
        }

        $questionObj = Question::updateOrCreate(
            [
                'id' => $data['id'],
            ],
            [
                'id' => $data['id'],
                'question_number' => $data['qno'][0],
                'question' => $data['question'][0],
                'photo' => $data['photo'] ?? null,
                'photo_link' => $data['photo_link'] ?? null,
                'notes' => $data['notes'][0],
                'level' => $data['level'],
                'option_a' => $data['option_a'][0],
                'option_b' => $data['option_b'][0],
                'option_c' => $data['option_c'][0],
                'option_d' => $data['option_d'][0],
                'answer' => $data['answer'],
                'language_id' => $data['language'][0],
                'category_id' => $data['module']['Category'][0],
                'sub_category_id' => $data['module']['Sub Category'][0],
                'subject_id' => $data['module']['Subject'][0],
                'topic_id' => $data['module']['Topic'][0],
                'question_bank_id' => null
            ]
        );

        session()->flash('success', 'Question updated successfully!');

        return redirect()->route('question.index');
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

        // return redirect()->route('question.index');
    }

    public function destroyTranslationQuestion(string $id)
    {
        $question = TranslatedQuestions::findOrFail($id);
        if (isset($question)) {

            $question->delete();
        }

        session()->flash('success', 'Question deleted successfully!');

        // return redirect()->route('question.index');
    }

    public function destroy(string $id)
    {
        $translateQuestion = TranslatedQuestions::where('question_id', $id);
        $question = Question::find($id);

        $translateQuestion->delete();
        $question->delete();

        return redirect()->route('question.index');
    }

    public function getQuestions(Request $request)
    {
        $query = Question::query();

        $translation_questions_query = TranslatedQuestions::query();

        if ($request->has('language_id') && isset($request->language_id)) {
            $language_id = $request->language_id;
            $translation_questions_query->where('language_id', $request->language_id);
        }

        if ($request->has('category_id') && isset($request->category_id)) {
            $category_id = $request->category_id;
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('sub_category_id') && isset($request->sub_category_id)) {
            $sub_category_id = $request->sub_category_id;
            $query->where('sub_category_id', $request->sub_category_id);
        }

        if ($request->has('subject_id') && isset($request->subject_id)) {
            $subject_id = $request->subject_id;
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->has('topic_id') && isset($request->topic_id)) {
            $topic_id = $request->topic_id;
            $query->where('topic_id', $request->topic_id);
        }

        $query->with(['category', 'subCategory', 'subject', 'topic']);

        $translation_questions_query->with(['language', 'question']);


        $questions = $query->get();

        // Check if there are any questions fetched
        if ($questions->isEmpty()) {
            return response()->json([]);
        }

        // Fetch translated questions based on the main questions
        $translatedQuestions = $translation_questions_query
            ->whereIn('question_id', $questions->pluck('id'))
            ->with(['question', 'language'])
            ->get();

        $sortColumn = request()->get('sort', 'id');
        $sortDirection = request()->get('direction', 'asc');
        $translatedQuestions = $translatedQuestions->orderBy($sortColumn, $sortDirection);

        // Paginate results
        $translatedQuestions = $translatedQuestions->paginate(request()->get('per_page', 10));

        return view('question-bank.index', compact('translatedQuestions', 'language_id', 'category_id', 'sub_category_id', 'subject_id', 'topic_id', 'languages', 'categories', 'questions', 'sortColumn', 'sortDirection'));

        // return response()->json(data: $translatedQuestions);
    }

    public function export(Request $request)
    {
        $languages = $request->input('languages', []);
        $query = Question::query();
        $translated_questions_query = TranslatedQuestions::whereIn('language_id', $languages);

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

        $question_query = $query->with(['topic', 'subject', 'subCategory', 'category'])
            ->get();

        $translatedQuestions = $translated_questions_query
            ->whereIn('question_id', $question_query->pluck('id'))
            ->with(['question', 'language'])
            ->get();

        $questions = [];

        foreach ($translatedQuestions as $translatedQuestion) {
            $questionId = $translatedQuestion->question_id;

            if (!isset($questions[$questionId])) {
                $questions[$questionId] = [
                    'question' => [],
                    'option_a' => [],
                    'option_b' => [],
                    'option_c' => [],
                    'option_d' => [],
                    'answer' => $translatedQuestion->question->answer,
                    'level' => $translatedQuestion->question->level,
                    'photo' => isset(explode("/", $translatedQuestion->question->photo)[2])  ? explode("/", $translatedQuestion->question->photo)[2] : $translatedQuestion->question->photo,
                    'photo_link' => $translatedQuestion->question->photo_link,
                    'category' => $translatedQuestion->question->category_id ?? '',
                    'subCategory' => $translatedQuestion->question->sub_category_id ?? '',
                    'subject' => $translatedQuestion->question->subject_id ?? '',
                    'topic' => $translatedQuestion->question->topic_id ?? '',
                    'qno' => $translatedQuestion->question->question_number,
                    'notes' => $translatedQuestion->question->notes,
                    'id' => $translatedQuestion->question_id,
                ];
            }

            $questions[$questionId]['language'][$translatedQuestion->language->id] = $translatedQuestion->language->name;
            $questions[$questionId]['question'][$translatedQuestion->language->id] = $translatedQuestion->question_text;
            $questions[$questionId]['option_a'][$translatedQuestion->language->id] = $translatedQuestion->option_a;
            $questions[$questionId]['option_b'][$translatedQuestion->language->id] = $translatedQuestion->option_b;
            $questions[$questionId]['option_c'][$translatedQuestion->language->id] = $translatedQuestion->option_c;
            $questions[$questionId]['option_d'][$translatedQuestion->language->id] = $translatedQuestion->option_d;
        }
        return Excel::download(new QuestionsExport($questions,  $languages), 'questions.xlsx');
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
            // Perform validation checks for required IDs
            foreach ($languageIds as $languageId) {
                if (!$this->getLanguageId($languageId)) {
                    return back()->with('error', 'Language -  "' . $languageId . '" not available');
                }
            }
            if (!$this->getCategoryId($row['category'])) {
                return back()->with('error', 'Category - "' . $row['category'] . '" not available');
            }
            if (!$this->getSubCategoryId($row['subcategory'])) {
                return back()->with('error', 'Subcategory - "' . $row['sub_category'] . '" not available');
            }
            if (!$this->getSubjectId($row['subject'])) {
                return back()->with('error', 'Subject - "' . $row['subject'] . '" not available');
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
        $subjects = Subject::where('sub_category_id', $subCategoryId)->get();

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

    public function printToConsole($data)
    {
        echo "<script>console.log(" . json_encode($data) . ");</script>";
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
}
