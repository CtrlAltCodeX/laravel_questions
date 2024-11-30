<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\SubCategory;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $languages = Language::all();
        $categories = Category::all();
        $subcategories = SubCategory::all();
        
        $language_id = request()->language_id;
        $category_id = request()->category_id;
        $subcategory_id = request()->sub_category_id;

        $query = Subject::query();

        if ($subcategory_id) {
            $query->where('sub_category_id', $subcategory_id);
        }
        if ($category_id) {
            $query->whereHas('subCategory', function ($query) use ($category_id) {
                $query->where('category_id', $category_id);
            });
        }
        if ($language_id) {
            $query->whereHas('subCategory.category', function ($query) use ($language_id) {
                $query->where('language_id', $language_id);
            });
        }

        $subjects = $query
            ->with('subCategory.category.language')
            ->paginate(10);

        return view('subjects.index', compact('subjects', 'categories', 'subcategories', 'languages', 'language_id', 'category_id', 'subcategory_id'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sub_categories = SubCategory::all();

        $languages = Language::all();

        return view('subjects.create', compact('sub_categories', 'languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        request()->validate([
            'name' => 'required',
            'sub_category_id' => 'required'
        ]);

        $subject = Subject::create(request()->all());

        if ($request->hasFile('photo')) {
            $fileName = "site/" . time() . "_photo.jpg";

            $request->file('photo')->storePubliclyAs('public', $fileName);

            $subject->photo = $fileName;

            $subject->save();
        }

        session()->flash('success', 'Subject Successfully Created');

        return redirect()->route('subject.index');
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
        $subject = Subject::with('subCategory')
            ->find($id);

        $sub_categories = SubCategory::all();

        $languages = Language::all();

        return view('subjects.edit', compact('subject', 'sub_categories', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        request()->validate([
            'name' => 'required',
            'sub_category_id' => 'required'
        ]);

        $subject = Subject::find($id);
        $subject->update(request()->all());

        if ($request->hasFile('photo')) {
            $fileName = "site/" . time() . "_photo.jpg";

            $request->file('photo')->storePubliclyAs('public', $fileName);

            $subject->photo = $fileName;

            $subject->save();
        }


        session()->flash('success', 'Subject Successfully Updated');

        return redirect()->route('subject.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Subject::find($id)
            ->delete();

        session()->flash('success', 'Subject Successfully Deleted');

        return redirect()->route('subject.index');
    }
}
