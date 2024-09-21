<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Language;
use App\Models\SubCategory;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Http\Request;

class QuestionBankApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $languages = Language::all();
        $categories = Category::all();
        $sub_categories = SubCategory::all();
        $subjects = Subject::all();
        $topics = Topic::all();
        return view("question-bank-api.index", compact('languages', 'categories','sub_categories','subjects', 'topics'));
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
}
