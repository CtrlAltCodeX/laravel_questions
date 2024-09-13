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
use Maatwebsite\Excel\Facades\Excel;

class QuestionBankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $questionBank = QuestionBank::with(
            'language', 
            'category', 
            'subCategory', 
            'subject', 
            'topic'
        )->get();

        $questions = Question::with('question_bank')->get();

        $languages = Language::all();
        $categories = Category::all();
        $subCategories = SubCategory::all();
        $subjects = Subject::all();
        $topics = Topic::all();
        return view('question-bank.index', compact('questionBank', 'questions', 'languages', 'categories', 'subCategories', 'subjects', 'topics'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $questions = QuestionBank::with(
            'language', 
            'category', 
            'subCategory', 
            'subject', 
            'topic'
        )->get();

        $languages = Language::all();
        $categories = Category::all();
        $subCategories = SubCategory::all();
        $subjects = Subject::all();
        $topics = Topic::all();

        return view('question-bank.create', compact('questions','languages', 'categories', 'subCategories', 'subjects', 'topics'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        dd($request->all());
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

        $questionBank = QuestionBank::updateOrCreate([
            'language_id' => $data['module']['Language'][0],
            'category_id' => $data['module']['Category'][0],
            'sub_category_id' => $data['module']['Sub Category'][0],
            'subject_id' => $data['module']['Subject'][0],
            'topic_id' => $data['module']['Topic'][0],
        ]);
        
        foreach ($data['question'] as $index => $question) {
            
            Question::updateOrCreate(
                ['id' => $data['id'][$index]],
                [
                'question' => $question,
                'photo' => $data['photo'][$index],
                'photo_link' => $data['photo_link'][$index],
                'notes' => $data['notes'][$index],
                'level' => $data['level'][$index],
                'option_a' => $data['option_a'][$index],
                'option_b' => $data['option_b'][$index],
                'option_c' => $data['option_c'][$index],
                'option_d' => $data['option_d'][$index],
                'answer' => $data['answer'][$index],
                'question_bank_id' => $questionBank->id,
            ]);
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
        
        $question = Question::where('id', $id)->with('question_bank') ->first();

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

        foreach ($data['question'] as $index => $question) {
            # code...
            
            $questionObj = Question::updateOrCreate(
                ['id' => $data['id'][$index]],
                [
                    'question' => $question,
                    'photo' => $data['photo'][$index] ?? null,
                    'photo_link' => $data['photo_link'][$index] ?? null,
                    'notes' => $data['notes'][$index],
                    'level' => $data['level'][$index],
                    'option_a' => $data['option_a'][$index],
                    'option_b' => $data['option_b'][$index],
                    'option_c' => $data['option_c'][$index],
                    'option_d' => $data['option_d'][$index],
                    'answer' => $data['answer'][$index],
                    'question_bank_id' => $questionBank->id
                ]
            );
        
            if ($request->hasFile('photo.' . $index)) {
                $fileName = "site/" . time() . "_photo.jpg";
                $request->file('photo.' . $index)->storePubliclyAs('public', $fileName);
                $questionObj->photo = $fileName;
                $questionObj->save();
            }

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
        if(isset($question)){

            $question->delete();
        }

        session()->flash('success', 'Question deleted successfully!');

        // return redirect()->route('question.index');
    }

    public function destroy(string $id)
    {
        $questionBank = QuestionBank::findOrFail($id);
        if(isset($questionBank)){
            $questions = Question::where('question_bank_id', $id)->get();
            foreach ($questions as $question) {
                $question->delete();
            }
            $questionBank->delete();
        }

        session()->flash('success', 'Question Bank deleted successfully!');

        return redirect()->route('question.index');
    }

    public function getQuestions(Request $request) {
        $query = QuestionBank::query();
    
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
    
        $question_banks = $query->get();

        $questions = [];

        if ($question_banks->isNotEmpty()) {
            foreach ($question_banks as $question_bank) {
                $bankQuestions = Question::where('question_bank_id', $question_bank->id)->get();
                $questions = array_merge($questions, $bankQuestions->toArray());
            }
        }
        
        // $this->consoleLog($question_bank->id);  
        // $questions = isset($question_bank) ? Question::where('question_bank_id', $question_bank->id)->with('question_bank')->get() : [];

    
        return response()->json($questions);
    }

    public function export(Request $request)
    {
        $query = QuestionBank::query();
    
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
    
        $question_banks = $query->with(['language', 'topic', 'subject', 'subCategory', 'category'])->get();
    
        $questions = [];
    
        foreach ($question_banks as $question_bank) {
            $bankQuestions = Question::where('question_bank_id', $question_bank->id)
                ->get()
                ->makeHidden(['id', 'created_at', 'updated_at', 'question_bank_id'])
                ->toArray();
    
            foreach ($bankQuestions as $question) {
                $questions[] = array_merge($question, [
                    'language' => $question_bank->language->name ?? '',
                    'category' => $question_bank->category->name ?? '',
                    'subCategory' => $question_bank->subCategory->name ?? '',
                    'subject' => $question_bank->subject->name ?? '',
                    'topic' => $question_bank->topic->name ?? '',
                ]);
            }
        }
    
        return Excel::download(new QuestionsExport($questions), 'questions.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);

        $questions = Excel::import(new QuestionsImport, $request->file('file'));

        dd($questions);
        return back()->with('success', 'Questions imported successfully.');
    }

    public function getCategories($languageId) {
        $categories = Category::where('language_id', $languageId)->get();
        return response()->json($categories);
    }
    
    public function getSubCategories($categoryId) {
        $subCategories = SubCategory::where('category_id', $categoryId)->get();
        return response()->json($subCategories);
    }
    
    public function getSubjects($subCategoryId) {
        $subjects = Subject::where('sub_category_id', $subCategoryId)->get();
        return response()->json($subjects);
    }
    
    public function getTopics($subjectId) {
        $topics = Topic::where('subject_id', $subjectId)->get();
        return response()->json($topics);
    }

    // create a function to console log the data
    public function consoleLog($data) {
        echo "<script>console.log('".$data."')</script>";
    }

    
}
