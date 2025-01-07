<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Language;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SubCategoryExport;
use App\Exports\SampleSubCategoryExport;
use App\Imports\SubCategoryImport;

class SubCategoryController extends Controller
{
    public function index(Request $request)
    {
        $languages = Language::all();
        $categories = Category::all();
    
        $language_id = $request->get('language_id');
        $category_id = $request->get('category_id');
    
        // Sorting logic
        $sortColumn = $request->get('sort', 'id'); // Default column
        $sortDirection = $request->get('direction', 'asc'); // Default direction
    
        $query = SubCategory::query()->with('category.language');
    
        if ($category_id) {
            $query->where('category_id', $category_id);
        }
    
        if ($language_id) {
            $query->whereHas('category', function ($query) use ($language_id) {
                $query->where('language_id', $language_id);
            });
        }
    
        // Handle sorting for related fields
        if (in_array($sortColumn, ['id', 'name', 'category', 'language'])) {
            $query->join('categories', 'sub_categories.category_id', '=', 'categories.id')
                  ->join('languages', 'categories.language_id', '=', 'languages.id')
                  ->select('sub_categories.*')
                  ->orderBy(
                      match ($sortColumn) {
                          'language' => 'languages.name',
                          'category' => 'categories.name',
                          default => 'sub_categories.' . $sortColumn,
                      },
                      $sortDirection
                  );
        }
    
        $sub_categories = $query->paginate(10);
    
        return view('sub-category.index', compact(
            'sub_categories', 
            'categories', 
            'languages', 
            'language_id', 
            'category_id', 
            'sortColumn', 
            'sortDirection'
        ));
    }  

  
    public function export()
    {
        return Excel::download(new SubCategoryExport, 'Subcategories.xlsx');
    }
   
   
    public function sample()
    {
       return Excel::download(new SampleSubCategoryExport, 'SampleSubCategories.xlsx');
    }
   

    
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx',
        ]);
    
        $importer = new SubCategoryImport();
    
        try {
            Excel::import($importer, $request->file('file'));
        } catch (\Exception $e) {
            return redirect()->route('sub-category.index')
                ->with('import_errors', [ $e->getMessage()]);
        }
    
        if (!empty($importer->errors)) {
            return redirect()->route('sub-category.index')
                ->with('import_errors', $importer->errors);
        }
    
        return redirect()->route('sub-category.index')->with('success', 'Sub Categories imported successfully!');
    }
    
    

    public function create()
    {
        $categories = Category::all();

        $languages = Language::all();

        return view('sub-category.create', compact('categories', 'languages'));
    }

   
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

        // return redirect()->route('sub-category.index');
        return response()->json(['success' => true, 'message' => 'Successfully Created', 'subcategory' => $subcategory]);

    }

    public function edit(string $id)
    {
        $sub_categories = SubCategory::find($id);

        $categories = Category::all();

        $languages = Language::all();

        return view('sub-category.edit', compact('sub_categories', 'categories', 'languages'));
    }


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

        return response()->json(['success' => true, 'message' => 'Successfully Updated', 'subcategory' => $subcategory]);
        // return redirect()->route('sub-category.index');
    }

  
    public function destroy(string $id)
    {
        SubCategory::find($id)
            ->delete();

        return redirect()->route('sub-category.index');
    }
}
