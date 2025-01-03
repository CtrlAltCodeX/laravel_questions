<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\SubCategory;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SubjectExport;
use App\Exports\SampleSubjectExport;
use App\Imports\SubjectImport;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $languages = Language::all();
        $categories = Category::all();
        $subcategories = SubCategory::all();
    
        $language_id = $request->get('language_id');
        $category_id = $request->get('category_id');
        $subcategory_id = $request->get('sub_category_id');
    
        // Sorting logic
        $sortColumn = $request->get('sort', 'id'); // Default column
        $sortDirection = $request->get('direction', 'asc'); // Default direction
    
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
    
        // Handle sorting for related fields
        if (in_array($sortColumn, ['id', 'name', 'sub_category', 'category', 'language'])) {
            $query->with(['subCategory.category.language']);
            $query->leftJoin('sub_categories', 'subjects.sub_category_id', '=', 'sub_categories.id')
                ->leftJoin('categories', 'sub_categories.category_id', '=', 'categories.id')
                ->leftJoin('languages', 'categories.language_id', '=', 'languages.id');
    
            $query->orderBy(
                match ($sortColumn) {
                    'language' => 'languages.name',
                    'category' => 'categories.name',
                    'sub_category' => 'sub_categories.name',
                    default => 'subjects.' . $sortColumn,
                },
                $sortDirection
            );
        }
    
        $subjects = $query->select('subjects.*')->paginate(10);
    
        return view('subjects.index', compact(
            'subjects',
            'categories',
            'subcategories',
            'languages',
            'language_id',
            'category_id',
            'subcategory_id',
            'sortColumn',
            'sortDirection'
        ));
    }
        

  
    public function export()
    {
        return Excel::download(new SubjectExport, 'Subject.xlsx');
    }
   
   
    public function sample()
    {
       return Excel::download(new SampleSubjectExport, 'SampleSubject.xlsx');
    }
   
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx',
        ]);
        Excel::import(new SubjectImport, $request->file('file'));
   
        return redirect()->route('subject.index')->with('success', 'Subject imported successfully!');
    }

  
    public function create()
    {
        $sub_categories = SubCategory::all();

        $languages = Language::all();

        return view('subjects.create', compact('sub_categories', 'languages'));
    }

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

        // session()->flash('success', 'Subject Successfully Created');
        return response()->json(['success' => true, 'message' => 'Subject Successfully Created', 'subject' => $subject]);
        // return redirect()->route('subject.index');
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


        // session()->flash('success', 'Subject Successfully Updated');
        return response()->json(['success' => true, 'message' => 'Subject Successfully Updated', 'subject' => $subject]);
        // return redirect()->route('subject.index');
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
