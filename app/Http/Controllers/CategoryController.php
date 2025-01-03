<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Language;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CategoryExport;
use App\Exports\SampleCategoryExport;
use App\Imports\CategoryImport;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $languages = Language::all();
        $query = Category::with('language');
   
        $sortColumn = $request->get('sort', 'id'); 
        $sortDirection = $request->get('direction', 'asc'); 
    
        if ($sortColumn === 'language') {
            $query = $query->join('languages', 'categories.language_id', '=', 'languages.id')
                           ->select('categories.*', 'languages.name as language_name')
                           ->orderBy('language_name', $sortDirection);
        } elseif (in_array($sortColumn, ['id', 'name'])) {
            $query = $query->orderBy($sortColumn, $sortDirection);
        }
 
        if ($language_id = $request->get('language_id')) {
            $query = $query->where('language_id', $language_id);
        }
    
        $categorys = $query->paginate(10);
    
        return view('categorys.index', compact('categorys', 'languages', 'sortColumn', 'sortDirection', 'language_id'));
    }
    

 public function export()
 {
     return Excel::download(new CategoryExport, 'categories.xlsx');
 }


 public function sample()
 {
    return Excel::download(new SampleCategoryExport, 'SampleCategories.xlsx');
 }


 public function import(Request $request)
 {
     $request->validate([
         'file' => 'required|file|mimes:xlsx',
     ]);

     Excel::import(new CategoryImport, $request->file('file'));

     return redirect()->route('category.index')->with('success', 'Categories imported successfully!');
 }


    public function create()
    {
        $languages = Language::all();

        return view('categorys.create', compact('languages'));
    }

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

        return response()->json(['success' => true, 'message' => 'Successfully Created', 'category' => $category]);

    }

    public function edit(string $id)
    {
        $category = Category::find($id);

        $languages = Language::all();

        return view('categorys.edit', compact('category', 'languages'));
    }

   
    public function update(Request $request, string $id)
    {

        
        $request->validate([
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

        return response()->json(['success' => true, 'message' => 'Successfully Updated', 'category' => $category]);
    }


    public function show(string $id)
    {
        //
    }

    
    public function destroy(string $id)
    {
        Category::find($id)
            ->delete();

        session()->flash('success', 'Category Successfully Deleted');

        return redirect()->route('category.index');
    }
}
