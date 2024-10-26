<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Language;
use App\Models\SubCategory;
use Illuminate\Http\Request;

use App\Models\Topic;
use App\Models\Subject;

class TopicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $languages = Language::all();
        $categories = Category::all();
        $subcategories = SubCategory::all();
        $subjects = Subject::all();

        $subject_id = request()->subject_id;
        $subcategory_id = request()->sub_category_id;
        $category_id = request()->category_id;
        $language_id = request()->language_id;

        $query = Topic::query();

        if ($subject_id) {
            $query->where('subject_id', $subject_id);
        }
        if ($subcategory_id) {
            $query->whereHas('subject', function ($query) use ($subcategory_id) {
                $query->where('sub_category_id', $subcategory_id);
            });
        }
        if ($category_id) {
            $query->whereHas('subject.subCategory', function ($query) use ($category_id) {
                $query->where('category_id', $category_id);
            });
        }
        if ($language_id) {
            $query->whereHas('subject.subCategory.category', function ($query) use ($language_id) {
                $query->where('language_id', $language_id);
            });
        }

        $topics = $query->get();

        return view('topics.index', compact('topics', 'categories', 'subcategories', 'subjects', 'languages', 'subject_id', 'subcategory_id', 'category_id', 'language_id'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $subjects = Subject::all();

        $languages = Language::all();

        return view('topics.create', compact('subjects', 'languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        request()->validate([
            'name' => 'required',
            'subject_id' => 'required'
        ]);

        $topic = Topic::create(request()->all());

        if ($request->hasFile('photo')) {
            $fileName = "site/" . time() . "_photo.jpg";

            $request->file('photo')->storePubliclyAs('public', $fileName);

            $topic->photo = $fileName;

            $topic->save();
        }


        session()->flash('success', 'Topic Successfully Created');

        return redirect()->route('topic.index');
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
        $topic = Topic::with('subject')->find($id);

        $subjects = Subject::all();

        $languages = Language::all();

        return view('topics.edit', compact('topic', 'subjects', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        request()->validate([
            'name' => 'required',
            'subject_id' => 'required'
        ]);

        $topic =  Topic::find($id);

        $topic->update(request()->all());

        if ($request->hasFile('photo')) {
            $fileName = "site/" . time() . "_photo.jpg";

            $request->file('photo')->storePubliclyAs('public', $fileName);

            $topic->photo = $fileName;

            $topic->save();
        }


        session()->flash('success', 'Topic Successfully Updated');

        return redirect()->route('topic.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Topic::destroy($id);

        session()->flash('success', 'Topic Successfully Deleted');

        return redirect()->route('topic.index');
    }
}
