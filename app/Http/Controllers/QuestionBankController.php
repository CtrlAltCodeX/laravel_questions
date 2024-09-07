<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuestionBank;

class QuestionBankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $questions = QuestionBank::with('
            language', 
            'category', 
            'subCategory', 
            'subject', 
            'topic'
        )->get();
        return view('question-bank.index', compact('questions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = Language::all();
        $categories = Category::all();
        $subCategories = SubCategory::all();
        $subjects = Subject::all();
        $topics = Topic::all();

        return view('question-bank.create', compact('languages', 'categories', 'subCategories', 'subjects', 'topics'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required',
            'option_a' => 'required',
            'option_b' => 'required',
            'option_c' => 'required',
            'option_d' => 'required',
            'answer' => 'required',
            'language_id' => 'required',
            'category_id' => 'required',
            'sub_category_id' => 'required',
            'subject_id' => 'required',
            'topic_id' => 'required',
        ]);

        QuestionBank::create($request->all());

        session()->flash('success', 'Question added successfully!');

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
        
        $questionBank = QuestionBank::find($id);
        $languages = Language::all();
        $categories = Category::all();
        $subCategories = SubCategory::all();
        $subjects = Subject::all();
        $topics = Topic::all();
        return view('question-bank.edit', compact('questionBank', 'languages', 'categories', 'subCategories', 'subjects', 'topics'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {   
        $request->validate([
            'question' => 'required',
            'option_a' => 'required',
            'option_b' => 'required',
            'option_c' => 'required',
            'option_d' => 'required',
            'answer' => 'required',
            'language_id' => 'required',
            'category_id' => 'required',
            'sub_category_id' => 'required',
            'subject_id' => 'required',
            'topic_id' => 'required',
        ]);

        $questionBank = QuestionBank::find($id);
        $questionBank->update($request->all());

        session()->flash('success', 'Question updated successfully!');

        return redirect()->route('question.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $questionBank = QuestionBank::find($id);
        $questionBank->delete();

        session()->flash('success', 'Question deleted successfully!');

        return redirect()->route('question.index');
    }
}
