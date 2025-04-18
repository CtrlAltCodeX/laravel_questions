<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Offer;

use App\Models\Category;
use App\Models\Language;
use App\Models\SubCategory;
use App\Models\Subject;

class OfferController extends Controller
{
    /**
     * @OA\Get(
     *     path="/offers",
     *     summary="Get a list of offers with filters and sorting",
     *     tags={"Offers"},
     *     @OA\Parameter(name="subject_id", in="query", description="Filter by Subject ID", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="sub_category_id", in="query", description="Filter by Sub Category ID", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="category_id", in="query", description="Filter by Category ID", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="language_id", in="query", description="Filter by Language ID", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="sort", in="query", description="Sort column", @OA\Schema(type="string")),
     *     @OA\Parameter(name="direction", in="query", description="Sort direction (asc or desc)", @OA\Schema(type="string")),
     *     @OA\Parameter(name="data", in="query", description="Pagination or 'all'", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="List of offers"),
     * )
     */
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

    public function create()
    {
        //
    }

    /**
     * @OA\Post(
     *     path="/offers",
     *     summary="Create a new offer",
     *     tags={"Offers"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "subject_id"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="subject_id", type="integer"),
     *             @OA\Property(property="banner", type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Offer Successfully Created"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
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

    /**
     * @OA\Put(
     *     path="/offers/{id}",
     *     summary="Update an existing offer",
     *     tags={"Offers"},
     *     @OA\Parameter(name="id", in="path", required=true, description="Offer ID", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "subject_id"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="subject_id", type="integer"),
     *             @OA\Property(property="banner", type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Offer Successfully Updated"),
     *     @OA\Response(response=404, description="Offer not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(Request $request, string $id)
    {
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

    public function edit(Offer $offer)
    {
        //
    }

    /**
     * @OA\Get(
     *     path="/api/offers",
     *     summary="Get offers through API with filters and sorting",
     *     tags={"Offers"},
     *     @OA\Parameter(name="subject_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="sub_category_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="category_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="language_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="sort", in="query", description="Sort column", @OA\Schema(type="string")),
     *     @OA\Parameter(name="direction", in="query", description="Sort direction", @OA\Schema(type="string")),
     *     @OA\Parameter(name="data", in="query", description="'all' or number of records per page", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Offers fetched successfully"),
     * )
     */
    public function getOffersApi(Request $request)
    {
        $subject_id = $request->get('subject_id');
        $subcategory_id = $request->get('sub_category_id');
        $category_id = $request->get('category_id');
        $language_id = $request->get('language_id');
        $sortColumn = $request->get('sort', 'offers.id');
        $sortDirection = $request->get('direction', 'desc');

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

        $sortableColumns = [
            'id' => 'offers.id',
            'name' => 'offers.name',
            'language' => 'languages.name',
            'category' => 'categories.name',
            'sub_category' => 'sub_categories.name',
            'subject' => 'subjects.name'
        ];

        if (array_key_exists($sortColumn, $sortableColumns)) {
            $query->join('subjects', 'offers.subject_id', '=', 'subjects.id')
                ->join('sub_categories', 'subjects.sub_category_id', '=', 'sub_categories.id')
                ->join('categories', 'sub_categories.category_id', '=', 'categories.id')
                ->join('languages', 'categories.language_id', '=', 'languages.id');

            $query->orderBy($sortableColumns[$sortColumn], $sortDirection);
        }

        if ($request->get('data') == 'all') {
            $offers = $query->get();
        } else {
            $offers = $query->paginate($request->get('data', 10));
        }

        return response()->json([
            'success' => true,
            'message' => 'Offers fetched successfully',
            'data' => $offers
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/offers/{id}",
     *     summary="Delete an offer",
     *     tags={"Offers"},
     *     @OA\Parameter(name="id", in="path", required=true, description="Offer ID", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Offer Successfully Deleted"),
     *     @OA\Response(response=404, description="Offer not found"),
     * )
     */
    public function destroy(string $id)
    {
        Offer::destroy($id);

        session()->flash('success', 'Offer Successfully Deleted');

        return redirect()->route('offers.index');
    }
}
