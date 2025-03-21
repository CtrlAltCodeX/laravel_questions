<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Offer;

use App\Models\Category;
use App\Models\Language;
use App\Models\SubCategory;
use App\Models\Subject;

class OfferController extends Controller {
    public function index(Request $request)
    {
        $languages = Language::all();
        $categories = Category::all();
        $subcategories = SubCategory::all();
        $subjects = Subject::all();

        $subject_id = $request->get('subject_id');
        $subcategory_id = $request->get('sub_category_id');
        $category_id = $request->get('category_id');
        $language_id = $request->get('language_id');
       
        $sortColumn = $request->get('sort', 'Offer.id');
        $sortDirection = $request->get('direction', 'desc');

        // Prepare the query
        $query = Offer::select('offers.*')->with('subject.subCategory.category.language');
  
        if ($subject_id) {
            $query->where('subject_id', $subject_id);
        }
        if ($subcategory_id) {
            $query->whereHas('subject', function ($q) use ($subcategory_id) {
                $q->where('sub_category_id', $subcategory_id);
            });
        }
        if ($category_id) {
            $query->whereHas('subject.subCategory', function ($q) use ($category_id) {
                $q->where('category_id', $category_id);
            });
        }
        if ($language_id) {
            $query->whereHas('subject.subCategory.category', function ($q) use ($language_id) {
                $q->where('language_id', $language_id);
            });
        }

        // Validate sort column
        $sortableColumns = ['id' => 'offers.id', 'name' => 'offers.name', 'language' => 'languages.name', 'category' => 'categories.name', 'sub_category' => 'sub_categories.name', 'subject' => 'subjects.name'];

        if (array_key_exists($sortColumn, $sortableColumns)) {
            // Include necessary joins for sorting by related columns
            $query->join('subjects', 'offers.subject_id', '=', 'subjects.id')
                ->join('sub_categories', 'subjects.sub_category_id', '=', 'sub_categories.id')
                ->join('categories', 'sub_categories.category_id', '=', 'categories.id')
                ->join('languages', 'categories.language_id', '=', 'languages.id');

            // Apply the sorting using the proper table aliases
            $query->orderBy($sortableColumns[$sortColumn], $sortDirection);
        }

        if (request()->data == 'all') {
            $offers = $query->get();
        } else {
            $offers = $query->paginate(request()->data);
        }

        $dropdown_list = [
            'Select Language' => $languages,
            'Select Category' => $categories,
            'Select Sub Category' => $subcategories ?? [],
            'Select Subject' => $subjects ?? [],
        ];

        // Return the view with all necessary data
        return view('offers.index', compact('offers', 'categories', 'subcategories', 'subjects', 'languages', 'subject_id', 'subcategory_id', 'category_id', 'language_id', 'sortColumn', 'sortDirection', 'dropdown_list'));
    }


    public function create() {
        //
    }

    
    public function store(Request $request)
    {
        request()->validate([
            'name' => 'required',
            'subject_id' => 'required'
        ]);


        $Offer = Offer::create(request()->all());

        if ($request->hasFile('banner')) {
            $fileName = "banner/" . time() . "_photo.jpg";

            $request->file('banner')->storePubliclyAs('public', $fileName);

            $Offer->banner = $fileName;

            $Offer->save();
        }

        session()->flash('success', 'Offer Successfully Created');

        return response()->json(['success' => true, 'message' => 'Offer Successfully Created', 'Offer' => $Offer]);
    }

    public function update(Request $request, string $id){
        request()->validate([
            'name' => 'required',
            'subject_id' => 'required'
        ]);

        $Offer =  Offer::find($id);
        $Offer->update(request()->all());
        

            if ($request->hasFile('banner')) {
                $fileName = "banner/" . time() . "_photo.jpg";
    
                $request->file('banner')->storePubliclyAs('public', $fileName);
    
                $Offer->banner = $fileName;
    
                $Offer->save();
            }
    

        return response()->json(['success' => true, 'message' => 'Offer Successfully Updated', 'Offer' => $Offer]);

       
    }


    public function edit(Offer $offer) {
//
    }


    public function destroy(string $id)
    {
        Offer::destroy($id);

        session()->flash('success', 'Offer Successfully Deleted');

        return redirect()->route('offers.index');
    }
}
