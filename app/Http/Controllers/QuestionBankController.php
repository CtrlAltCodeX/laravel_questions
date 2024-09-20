<?php

namespace App\Http\Controllers;

use App\Imports\QuestionsImport;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\QuestionBank;
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
        $questionBank = Question::all();

        $questions = Question::query();

        if ($search = request()->search) {
            $questions->orWhere('question', "%$search%")
                ->orWhere('option_a', 'LIKE', "%$search%")
                ->orWhere('option_b', 'LIKE', "%$search%")
                ->orWhere('option_c', 'LIKE', "%$search%")
                ->orWhere('option_d', 'LIKE', "%$search%")
                ->orWhere('answer', 'LIKE', "%$search%")
                ->orWhere('notes', 'LIKE', "%$search%");
        }

        if ($category = request()->category_id) {
            $questions->where('category_id', $category)
                ->where('sub_category_id', request()->sub_category_id);
        }

        $questions = $questions
            ->paginate(request()->per_page);

        $languages = Language::all();

        $categories = Category::all();

        $subCategories = SubCategory::where('category_id', request()->category_id)
            ->get();

        $subjects = Subject::all();

        $topics = Topic::all();

        return view('question-bank.index', compact('questionBank', 'questions', 'languages', 'categories', 'subCategories', 'subjects', 'topics'));
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

        foreach ($data['question'] as $index => $question) {
            if ($file = $data['photo'][$index]) {
                if ($file instanceof UploadedFile) {
                    $profileImage = time() . "." . $file->getClientOriginalExtension();

                    $file->move('storage/questions/', $profileImage);
                }
            }

            Question::updateOrCreate(
                ['id' => $data['id'][$index]],
                [
                    'question' => $question,
                    'photo' => '/storage/questions/' . $profileImage,
                    'photo_link' => $data['photo_link'][$index],
                    'notes' => $data['notes'][$index],
                    'level' => $data['level'][$index],
                    'option_a' => $data['option_a'][$index],
                    'option_b' => $data['option_b'][$index],
                    'option_c' => $data['option_c'][$index],
                    'option_d' => $data['option_d'][$index],
                    'answer' => $data['answer'][$index],
                    'language_id' => $data['module']['Language'][0],
                    'category_id' => $data['module']['Category'][0],
                    'sub_category_id' => $data['module']['Sub Category'][0],
                    'subject_id' => $data['module']['Subject'][0],
                    'topic_id' => $data['module']['Topic'][0],
                ]
            );
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

        $languages = Language::all();

        $categories = Category::all();

        $subCategories = SubCategory::all();

        $subjects = Subject::all();

        $topics = Topic::all();

        return view('question-bank.edit', compact('question', 'languages', 'categories', 'subCategories', 'subjects', 'topics'));
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

        $question = Question::findOrFail($id);
        $questionBank = QuestionBank::findOrFail($question->question_bank_id);

        $questionBank->update([
            'language_id' => $request['module']['Language'][0],
            'category_id' => $request['module']['Category'][0],
            'sub_category_id' => $request['module']['Sub Category'][0],
            'subject_id' => $request['module']['Subject'][0],
            'topic_id' => $request['module']['Topic'][0],
        ]);

        $questionObj = Question::updateOrCreate(
            ['id' => $data['id']],
            [
                'question' => $data['question'],
                'photo' => $data['photo'] ?? null,
                'photo_link' => $data['photo_link'] ?? null,
                'notes' => $data['notes'],
                'level' => $data['level'],
                'option_a' => $data['option_a'],
                'option_b' => $data['option_b'],
                'option_c' => $data['option_c'],
                'option_d' => $data['option_d'],
                'answer' => $data['answer'],
                'question_bank_id' => $questionBank->id
            ]
        );

        if ($request->hasFile('photo.' . $data['photo'])) {
            $fileName = "site/" . time() . "_photo.jpg";
            $request->file('photo.' . $data['photo'])->storePubliclyAs('public', $fileName);
            $questionObj->photo = $fileName;
            $questionObj->save();
        }

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

    public function destroy(string $id)
    {
        $question = Question::findOrFail($id);

        if (isset($question)) {
            $question->delete();
        }

        return redirect()->route('question.index');
    }

    public function getQuestions(Request $request)
    {
        $query = Question::query();

        if ($request->has('language_id')) {
            $query->where('language_id', $request->language_id);
        }
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->has('sub_category_id')) {
            $query->where('sub_category_id', $request->sub_category_id);
        }
        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        if ($request->has('topic_id')) {
            $query->where('topic_id', $request->topic_id);
        }

        $questions = $query->get();

        return response()->json($questions);
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

        $question_query = $query->with(['language', 'topic', 'subject', 'subCategory', 'category'])
            ->get();
    
        $questions = [];

        foreach ($question_query as $question) {
            $bankQuestions = Question::get()
                ->makeHidden(attributes: ['created_at', 'updated_at', 'question_bank_id'])
                ->toArray();

                $questionData = [
                    'question' => [],
                    'option_a' => [],
                    'option_b' => [],
                    'option_c' => [],
                    'option_d' => [],
                    'answer' => $question['answer'],
                    'level' => $question['level'],
                    'photo' => $question['photo'],
                    'photo_link' => $question['photo_link'],
                    'category' => $question->category->name ?? '',
                    'subCategory' => $question->subCategory->name ?? '',
                    'subject' => $question->subject->name ?? '',
                    'topic' => $question->topic->name ?? '',
                    'language' => $question->language->name ?? '',
                ];

                foreach ($languages as $languageId) {
                    $language = Language::find($languageId);
                    $translatedQuestion = $language->questions()->where('questions.id', $question['id'])->get();
                    $questionData['qno'] = $question['qno'] ?? '';
                    $questionData['notes'] = $question['notes'] ?? '';
                    if ($translatedQuestion->count() > 0) {
                        $questionData['question'][$language->id] = $translatedQuestion[0]->question;
                        $questionData['option_a'][$language->id] = $translatedQuestion[0]->option_a;
                        $questionData['option_b'][$language->id] = $translatedQuestion[0]->option_b;
                        $questionData['option_c'][$language->id] = $translatedQuestion[0]->option_c;
                        $questionData['option_d'][$language->id] = $translatedQuestion[0]->option_d;
                    } else {
                        $questionData['question'][$language->id] = $question['question'];
                        $questionData['option_a'][$language->id] = $question['option_a'];
                        $questionData['option_b'][$language->id] = $question['option_b'];
                        $questionData['option_c'][$language->id] = $question['option_c'];
                        $questionData['option_d'][$language->id] = $question['option_d'];
                    }
                }

                $questions[] = $questionData;
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

        foreach ($rows[0] as $row) {
            // Perform validation checks for required IDs
            if (!$this->getLanguageId($row['language'])) {
                return back()->with('error', 'Language -  "' . $row['language'] . '" not available');
            }
            if (!$this->getCategoryId($row['category'])) {
                return back()->with('error', 'Category - "' . $row['category'] . '" not available');
            }
            if (!$this->getSubCategoryId($row['sub_category'])) {
                return back()->with('error', 'Subcategory - "' . $row['sub_category'] . '" not available');
            }
            if (!$this->getSubjectId($row['subject'])) {
                return back()->with('error', 'Subject - "' . $row['subject'] . '" not available');
            }
            if (!$this->getTopicId($row['topic'])) {
                return back()->with('error', 'Topic - "' . $row['topic'] . '" not available');
            }
        }

        // If all validations pass, proceed with the import
        Excel::import(new QuestionsImport, $request->file('file'));

        return back()->with('success', 'Questions imported successfully.');
    }

    private function getLanguageId($name)
    {
        return \App\Models\Language::where('name', $name)->first()->id ?? null;
    }

    private function getCategoryId($name)
    {
        return \App\Models\Category::where('name', $name)->first()->id ?? null;
    }

    private function getSubCategoryId($name)
    {
        return \App\Models\SubCategory::where('name', $name)->first()->id ?? null;
    }

    private function getSubjectId($name)
    {
        return \App\Models\Subject::where('name', $name)->first()->id ?? null;
    }

    private function getTopicId($name)
    {
        return \App\Models\Topic::where('name', $name)->first()->id ?? null;
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
}
