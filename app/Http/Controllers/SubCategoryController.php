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
use App\Models\Offer;

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

        $query = SubCategory::with('category.language', 'question');

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

        if (request()->data == 'all') {
            $sub_categories = $query->get();
        } else {
            $sub_categories = $query->paginate(request()->data);
        }

        $dropdown_list = [
            'Select Language' => $languages,
            'Select Category' => $categories ?? [],
        ];

        return view('sub-category.index', compact(
            'sub_categories',
            'categories',
            'languages',
            'language_id',
            'category_id',
            'sortColumn',
            'sortDirection',
            'dropdown_list'
        ));
    }


    public function export(Request $request)
    {
        $languageId = $request->get('language_id');
        $categoryId = $request->get('category_id');

        return Excel::download(new SubCategoryExport($languageId,  $categoryId), 'Subcategories.xlsx');
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
            $rows = Excel::import($importer, $request->file('file'));

            foreach ($rows as $key => $row) {
                $rowCount = $key + 2;

                if (empty($row['category_id'])) {
                    return back()->with('error', 'Row: ' . $rowCount . '- Subcategory is required.');
                }

                if (!$this->getCategoryId($row['category_id'])) {
                    return back()->with('error', 'Subcategory - "' . $row['category_id'] . '" not available');
                }
            }
        } catch (\Exception $e) {
            return redirect()->route('sub-category.index')
                ->with('import_errors', [$e->getMessage()]);
        }

        if (!empty($importer->errors)) {
            return redirect()->route('sub-category.index')
                ->with('import_errors', $importer->errors);
        }

        return redirect()->route('sub-category.index')->with('success', 'Sub Categories imported successfully!');
    }

    private function getCategoryId($id)
    {
        return \App\Models\Category::find($id)->id ?? null;
    }

    public function create()
    {
        $categories = Category::all();

        $languages = Language::all();

        return view('sub-category.create', compact('categories', 'languages'));
    }


    public function store(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'category_id' => 'required',
            'name' => 'required',
        
        ]);
    
        // Prepare data for insertion
        $data = $request->only(['category_id', 'name', 'plan_type','status']);
    
        // Convert plans array to JSON string
        $data['plans'] = json_encode($request->plans);
    
        // Create subcategory with basic info
        $subcategory = SubCategory::create($data);
    
        // Handle image upload if exists
        if ($request->hasFile('photo')) {
            $fileName = "sub_category/" . time() . "_photo.jpg";
            $request->file('photo')->storePubliclyAs('public', $fileName);
            $subcategory->photo = $fileName;
            $subcategory->save();
        }
    
        return response()->json([
            'success' => true,
            'message' => 'SubCategory created successfully with plan details!',
            'subcategory' => $subcategory
        ]);
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
        // Validate incoming request
        $request->validate([
            'category_id' => 'required',
            'name' => 'required',
        ]);
    
        // Find the subcategory
        $subcategory = SubCategory::findOrFail($id);
    
        // Prepare the data for update
        $data = $request->only(['category_id', 'name', 'plan_type','status']);
    
        // Convert plans array to JSON string if provided
        if ($request->has('plans')) {
            $data['plans'] = json_encode($request->plans);
        }
    
        // Update subcategory
        $subcategory->update($data);
    
        // Handle image upload if exists
        if ($request->hasFile('photo')) {
            $fileName = "sub_category/" . time() . "_photo.jpg";
            $request->file('photo')->storePubliclyAs('public', $fileName);
            $subcategory->photo = $fileName;
            $subcategory->save();
        }
    
        return response()->json([
            'success' => true,
            'message' => 'SubCategory updated successfully!',
            'subcategory' => $subcategory
        ]);
    }
    

    public function getSubCategoryDetailsWithOffers($id)
{
   
    $subCategory = SubCategory::find($id);

    if (!$subCategory) {
        return response()->json([
            'status' => false,
            'message' => 'Sub Category not found'
        ], 404);
    }

   
    $offers = Offer::where('sub_category_id', $id)->get();


    return response()->json([
        'status' => true,
        'message' => 'Sub Category Details with Offers',
        'data' => [
            'sub_category' => $subCategory,
            'offers' => $offers
        ]
    ]);
}




    public function destroy(string $id)
    {
        SubCategory::find($id)
            ->delete();

        return redirect()->route('sub-category.index');
    }
}
