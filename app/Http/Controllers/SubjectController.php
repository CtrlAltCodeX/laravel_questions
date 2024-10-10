<?php

namespace App\Http\Controllers;

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
        $subcategories = SubCategory::all();
        
        if($subcategory_id = request()->subcategory_id){
            $subjects = Subject::where('sub_category_id', $subcategory_id)->get();
            return view('subjects.index', compact('subjects', 'subcategories', 'subcategory_id'));
        }else{
            $subjects = Subject::with('subCategory')->get();
            return view('subjects.index', compact('subjects', 'subcategories'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sub_categories = SubCategory::all();
        return view('subjects.create', compact('sub_categories'));
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

        return view('subjects.edit', compact('subject', 'sub_categories'));
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
