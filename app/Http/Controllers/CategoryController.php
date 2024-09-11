<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Language;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categorys = Category::with('language')
            ->get();

        return view('categorys.index', compact('categorys'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = Language::all();

        return view('categorys.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        request()->validate([
            'name' => 'required',
            'language_id' => 'required'
        ]);

        $category = Category::create(request()->all());

        if ($request->hasFile('photo')) {
            $fileName = "site/" . time() . "_photo.jpg";
        
            $request->file('photo')->storePubliclyAs('public', $fileName);
        
            $category->photo = $fileName;

            $category->save();
        }

        session()->flash('success', 'Category Successfully Created');

        return redirect()->route('category.index');
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
        $category = Category::find($id);

        $languages = Language::all();

        return view('categorys.edit', compact('category', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        request()->validate([
            'name' => 'required',
            'language_id' => 'required'
        ]);

        $category = Category::find($id);
        
        $category->update(request()->all());

        if ($request->hasFile('photo')) {
            $fileName = "site/" . time() . "_photo.jpg";
        
            $request->file('photo')->storePubliclyAs('public', $fileName);
        
            $category->photo = $fileName;
        
            $category->save();
        }
        

        return redirect()->route('category.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Category::find($id)
            ->delete();

        session()->flash('success', 'Category Successfully Deleted');

        return redirect()->route('category.index');
    }
}
