<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sub_categories = SubCategory::with('category')
            ->get();

        return view('sub-category.index', compact('sub_categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();

        return view('sub-category.create', compact('categories'));
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

        return view('sub-category.edit', compact('sub_categories', 'categories'));
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
