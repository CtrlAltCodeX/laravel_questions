<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Language;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        $languages = Language::all();
        $categories = Category::all();

        $language_id = request()->language_id;
        $category_id = request()->category_id;

        $query = SubCategory::query();

        if($category_id){
            $query->where('category_id', $category_id);
        }
        
        if($language_id){
            $query->whereHas('category', function($query) use($language_id){
                $query->where('language_id', $language_id);
            });
        }

        $sub_categories = $query->get();

        return view('sub-category.index', compact('sub_categories', 'categories', 'languages', 'language_id', 'category_id'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();

        $languages = Language::all();

        return view('sub-category.create', compact('categories', 'languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        request()->validate([
            'category_id' => 'required',
            'name' => 'required',
        ]);

        $subcategory = SubCategory::create(request()->all());

        if ($request->hasFile('photo')) {
            $fileName = "site/" . time() . "_photo.jpg";
        
            $request->file('photo')->storePubliclyAs('public', $fileName);
        
            $subcategory->photo = $fileName;

            $subcategory->save();
        }

        return redirect()->route('sub-category.index');
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
        $sub_categories = SubCategory::find($id);

        $categories = Category::all();

        $languages = Language::all();

        return view('sub-category.edit', compact('sub_categories', 'categories', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        request()->validate([
            'category_id' => 'required',
            'name' => 'required',
        ]);
        
        $subcategory = SubCategory::find($id);
            
        $subcategory->update(request()->all());

        if ($request->hasFile('photo')) {
            $fileName = "site/" . time() . "_photo.jpg";
        
            $request->file('photo')->storePubliclyAs('public', $fileName);
        
            $subcategory->photo = $fileName;

            $subcategory->save();
        }

        return redirect()->route('sub-category.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        SubCategory::find($id)
            ->delete();

        return redirect()->route('sub-category.index');
    }
}
