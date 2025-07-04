<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Offer;

use App\Models\Category;
use App\Models\Language;
use App\Models\SubCategory;
use App\Models\Subject;
use App\Models\Course;


class OfferController extends Controller
{

    //     public function index(Request $request)
    // {
    //     $languages = Language::all();
    //     $categories = Category::all();
    //     $subcategories = SubCategory::all();
    //     $subjects = Subject::all();

    //     $subject_id = $request->get('subject_id');
    //     $subcategory_id = $request->get('sub_category_id');
    //     $category_id = $request->get('category_id');
    //     $language_id = $request->get('language_id');

    //     $sortColumn = $request->get('sort', 'offers.id');
    //     $sortDirection = $request->get('direction', 'desc');

    //     // Build query manually without relationship
    //     $query = Offer::select(
    //         'offers.*',
    // 'subjects.name as subject_name',
    //         'sub_categories.name as sub_category_name',
    //         'categories.name as category_name',
    //         'languages.name as language_name'
    //     )
    //         ->leftJoin('subjects', 'offers.subject_id', '=', 'subjects.id')
    //         ->leftJoin('sub_categories', 'offers.sub_category_id', '=', 'sub_categories.id')
    //         ->leftJoin('categories', 'offers.category_id', '=', 'categories.id')
    //         ->leftJoin('languages', 'offers.language_id', '=', 'languages.id');

    //     // Apply filters
    // if ($subject_id) {
    //     $query->where('subject_id', $subject_id);
    // } elseif ($subcategory_id) {
    //     $query->whereHas('subject', function ($q) use ($subcategory_id) {
    //         $q->where('sub_category_id', $subcategory_id);
    //     });
    // } elseif ($category_id) {
    //     $query->whereHas('subject.subCategory', function ($q) use ($category_id) {
    //         $q->where('category_id', $category_id);
    //     });
    // } elseif ($language_id) {
    //     $query->whereHas('subject.subCategory.category', function ($q) use ($language_id) {
    //         $q->where('language_id', $language_id);
    //     });
    // }


    //     // Sortable columns
    //     $sortableColumns = [
    //         'id' => 'offers.id',
    //         'name' => 'offers.name',
    //         'language' => 'languages.name',
    //         'category' => 'categories.name',
    //         'sub_category' => 'sub_categories.name',
    //         'subject' => 'subjects.name',
    //     ];

    //     if (array_key_exists($sortColumn, $sortableColumns)) {
    //         $query->orderBy($sortableColumns[$sortColumn], $sortDirection);
    //     }

    //     $offers = request()->data == 'all' ? $query->get() : $query->paginate(request()->data);

    //     $dropdown_list = [
    //         'Select Language' => $languages,
    //         'Select Category' => $categories,
    //         'Select Sub Category' => $subcategories ?? [],
    //         'Select Subject' => $subjects ?? [],
    //     ];

    //     return view('offers.index', compact(
    //         'offers',
    //         'categories',
    //         'subcategories',
    //         'subjects',
    //         'languages',
    //         'subject_id',
    //         'subcategory_id',
    //         'category_id',
    //         'language_id',
    //         'sortColumn',
    //         'sortDirection',
    //         'dropdown_list'
    //     ));
    // }


    public function index(Request $request)
    {
        $sortColumn = $request->get('sort', 'id');
        $sortDirection = $request->get('direction', 'desc');
        $Courses = Course::all();

        $course = $request->get('course');

        $sortableColumns = [
            'id' => 'id',
            'name' => 'name',
            'status' => 'status',
            'course' => 'course',
            'valid_from' => 'valid_from',
            'valid_to' => 'valid_to',
        ];

        $query = Offer::query();

        // Filter by course if provided
        if ($course) {
            $query->where('course', $course);
        }

        // Sort
        if (array_key_exists($sortColumn, $sortableColumns)) {
            $query->orderBy($sortableColumns[$sortColumn], $sortDirection);
        }

        $offers = $request->data == 'all' ? $query->get() : $query->paginate($request->data);

// Enhance each offer with decoded subscription details
$offers->each(function ($offer) {
    $subscriptions = json_decode($offer->subscription, true);
    $offer->subscription_prepared = collect($subscriptions)->map(function ($data, $type) {
        return [
            'type' => ucfirst(str_replace('_', ' ', $type)),
            'discount' => $data['discount'] ?? '-',
            'upgrade' => $data['upgrade'] ?? '-',
        ];
    })->values(); // ensures it's a list of arrays
});
        return view('offers.index', compact(
            'offers',
            'sortColumn',
            'sortDirection',
            'course',
            'Courses'
        ));
    }

    // public function index(Request $request)
    // {
    //     $languages = Language::all();
    //     $categories = Category::all();
    //     $subcategories = SubCategory::all();
    //     $subjects = Subject::all();

    //     $subject_id = $request->get('subject_id');
    //     $subcategory_id = $request->get('sub_category_id');
    //     $category_id = $request->get('category_id');
    //     $language_id = $request->get('language_id');

    //     $sortColumn = $request->get('sort', 'Offer.id');
    //     $sortDirection = $request->get('direction', 'desc');

    //     // Prepare the query
    //     $query = Offer::select('offers.*')->with('subject.subCategory.category.language');

    //     if ($subject_id) {
    //         $query->where('subject_id', $subject_id);
    //     }
    //     if ($subcategory_id) {
    //         $query->whereHas('subject', function ($q) use ($subcategory_id) {
    //             $q->where('sub_category_id', $subcategory_id);
    //         });
    //     }
    //     if ($category_id) {
    //         $query->whereHas('subject.subCategory', function ($q) use ($category_id) {
    //             $q->where('category_id', $category_id);
    //         });
    //     }
    //     if ($language_id) {
    //         $query->whereHas('subject.subCategory.category', function ($q) use ($language_id) {
    //             $q->where('language_id', $language_id);
    //         });
    //     }

    //     // Validate sort column
    //     $sortableColumns = ['id' => 'offers.id', 'name' => 'offers.name', 'language' => 'languages.name', 'category' => 'categories.name', 'sub_category' => 'sub_categories.name', 'subject' => 'subjects.name'];

    //     if (array_key_exists($sortColumn, $sortableColumns)) {
    //         // Include necessary joins for sorting by related columns
    //         $query->join('subjects', 'offers.subject_id', '=', 'subjects.id')
    //             ->join('sub_categories', 'subjects.sub_category_id', '=', 'sub_categories.id')
    //             ->join('categories', 'sub_categories.category_id', '=', 'categories.id')
    //             ->join('languages', 'categories.language_id', '=', 'languages.id');

    //         // Apply the sorting using the proper table aliases
    //         $query->orderBy($sortableColumns[$sortColumn], $sortDirection);
    //     }

    //     if (request()->data == 'all') {
    //         $offers = $query->get();
    //     } else {
    //         $offers = $query->paginate(request()->data);
    //     }

    //     $dropdown_list = [
    //         'Select Language' => $languages,
    //         'Select Category' => $categories,
    //         'Select Sub Category' => $subcategories ?? [],
    //         'Select Subject' => $subjects ?? [],
    //     ];

    //     // Return the view with all necessary data
    //     return view('offers.index', compact('offers', 'categories', 'subcategories', 'subjects', 'languages', 'subject_id', 'subcategory_id', 'category_id', 'language_id', 'sortColumn', 'sortDirection', 'dropdown_list'));
    // }
    // public function store(Request $request)
    // {
    //     request()->validate([
    //         'name' => 'required',
    //         'subject_id' => 'required'
    //     ]);


    //     $Offer = Offer::create(request()->all());

    //     if ($request->hasFile('banner')) {
    //         $fileName = "banner/" . time() . "_photo.jpg";

    //         $request->file('banner')->storePubliclyAs('public', $fileName);

    //         $Offer->banner = $fileName;

    //         $Offer->save();
    //     }

    //     session()->flash('success', 'Offer Successfully Created');

    //     return response()->json(['success' => true, 'message' => 'Offer Successfully Created', 'Offer' => $Offer]);
    // }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'course' => 'required|array',
            'course.*' => 'exists:courses,id',
        
          
            'valid_from' => 'required|date',
            'valid_to' => 'required|date|after_or_equal:valid_from',
            'banner' => 'nullable|image',
            'status' => 'required|in:0,1',
        ]);

     $subscriptions = [];

foreach (['monthly', 'semi_annual', 'annual'] as $type) {
    if (isset($request->subscription[$type]['active'])) {
        $subscriptions[$type] = [
            'discount' => $request->subscription[$type]['discount'],
        ];

        // Only add 'upgrade' if NOT monthly
        if ($type !== 'monthly') {
            $subscriptions[$type]['upgrade'] = $request->subscription[$type]['upgrade'];
        }
    }
}


        $data = [
            'name' => $request->name,
          
            'valid_from' => $request->valid_from,
            'valid_to' => $request->valid_to,
            'status' => $request->status,
            'course' => json_encode($request->course), // save as JSON
            'subscription' => json_encode($request->subscription), // save as JSON
        ];

        // Handle banner upload
        if ($request->hasFile('banner')) {
            $file = $request->file('banner');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/offers'), $filename);
            $data['banner'] = $filename;
        }

        Offer::create($data);

        session()->flash('success', 'Offer Successfully Created');

        return response()->json(['success' => true, 'message' => 'Offer Successfully Created']);
    }


    public function update(Request $request, string $id)
{
    $request->validate([
        'name' => 'required',
        'course' => 'required|array',
        'course.*' => 'exists:courses,id',
        'valid_from' => 'required|date',
        'valid_to' => 'required|date|after_or_equal:valid_from',
        'banner' => 'nullable|image',
        'status' => 'required|in:0,1',
    ]);

    $offer = Offer::findOrFail($id);

    $subscriptions = [];

    foreach (['monthly', 'semi_annual', 'annual'] as $type) {
        if (isset($request->subscription[$type]['active'])) {
            $subscriptions[$type] = [
                'discount' => $request->subscription[$type]['discount'],
            ];

            if ($type !== 'monthly') {
                $subscriptions[$type]['upgrade'] = $request->subscription[$type]['upgrade'];
            }
        }
    }

    $data = [
        'name' => $request->name,
        'valid_from' => $request->valid_from,
        'valid_to' => $request->valid_to,
        'status' => $request->status,
        'course' => json_encode($request->course),
        'subscription' => json_encode($subscriptions), // ✅ fixed here
    ];

    if ($request->hasFile('banner')) {
        $file = $request->file('banner');
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/offers'), $filename);
        $data['banner'] = $filename;

        // Delete old file
        if ($offer->banner && file_exists(public_path('uploads/offers/' . $offer->banner))) {
            unlink(public_path('uploads/offers/' . $offer->banner));
        }
    }

    $offer->update($data);

    session()->flash('success', 'Offer Successfully Updated');

    return response()->json(['success' => true, 'message' => 'Offer Successfully Updated', 'Offer' => $offer]);
}


    public function edit(Offer $offer)
    {
        //
    }

    /**
     * @OA\Get(
     *     path="/api/offers",
     *     summary="Get offers with optional filters and sorting",
     *     description="Returns a paginated list of offers with optional filters for subject, sub-category, category, and language. Supports sorting and full data retrieval.",
     *     operationId="getOffersApi",
     *     tags={"Offers"},
     *
     *     @OA\Parameter(
     *         name="subject_id",
     *         in="query",
     *         description="Filter by subject ID",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="sub_category_id",
     *         in="query",
     *         description="Filter by sub-category ID",
     *         required=false,
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Filter by category ID",
     *         required=false,
     *         @OA\Schema(type="integer", example=3)
     *     ),
     *     @OA\Parameter(
     *         name="language_id",
     *         in="query",
     *         description="Filter by language ID",
     *         required=false,
     *         @OA\Schema(type="integer", example=4)
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Column to sort by (id, name, language, category, sub_category, subject)",
     *         required=false,
     *         @OA\Schema(type="string", example="id")
     *     ),
     *     @OA\Parameter(
     *         name="direction",
     *         in="query",
     *         description="Sort direction (asc or desc)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"}, example="desc")
     *     ),
     *     @OA\Parameter(
     *         name="data",
     *         in="query",
     *         description="Pass 'all' to fetch all records, or a number for pagination limit",
     *         required=false,
     *         @OA\Schema(type="string", example="10")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with filtered and sorted offers",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Offers fetched successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Paginated offers data or full list if 'data=all'",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=10),
     *                         @OA\Property(property="name", type="string", example="Diwali Special"),
     *                         @OA\Property(property="discount", type="number", format="float", example=20.5),
     *                         @OA\Property(property="subject_id", type="integer", example=1),
     *                         @OA\Property(property="valid_from", type="string", format="date", example="2025-06-01"),
     *                         @OA\Property(property="valid_to", type="string", format="date", example="2025-06-30")
     *                     )
     *                 ),
     *                 @OA\Property(property="last_page", type="integer", example=5),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=50)
     *             )
     *         )
     *     )
     * )
     */
    public function getOffersApi(Request $request)
    {
        // $subject_id = $request->get('subject_id');
        // $subcategory_id = $request->get('sub_category_id');
        // $category_id = $request->get('category_id');
        // $language_id = $request->get('language_id');
        // $sortColumn = $request->get('sort', 'offers.id');
        // $sortDirection = $request->get('direction', 'desc');

        // $query = Offer::select('offers.*')->with('subject.subCategory.category.language');

        // if ($subject_id) {
        //     $query->where('subject_id', $subject_id);
        // }

        // if ($subcategory_id) {
        //     $query->whereHas('subject', function ($q) use ($subcategory_id) {
        //         $q->where('sub_category_id', $subcategory_id);
        //     });
        // }

        // if ($category_id) {
        //     $query->whereHas('subject.subCategory', function ($q) use ($category_id) {
        //         $q->where('category_id', $category_id);
        //     });
        // }

        // if ($language_id) {
        //     $query->whereHas('subject.subCategory.category', function ($q) use ($language_id) {
        //         $q->where('language_id', $language_id);
        //     });
        // }

        // $sortableColumns = [
        //     'id' => 'offers.id',
        //     'name' => 'offers.name',
        //     'language' => 'languages.name',
        //     'category' => 'categories.name',
        //     'sub_category' => 'sub_categories.name',
        //     'subject' => 'subjects.name'
        // ];

        // if (array_key_exists($sortColumn, $sortableColumns)) {
        //     $query->join('subjects', 'offers.subject_id', '=', 'subjects.id')
        //         ->join('sub_categories', 'subjects.sub_category_id', '=', 'sub_categories.id')
        //         ->join('categories', 'sub_categories.category_id', '=', 'categories.id')
        //         ->join('languages', 'categories.language_id', '=', 'languages.id');

        //     $query->orderBy($sortableColumns[$sortColumn], $sortDirection);
        // }

        // if ($request->get('data') == 'all') {
        //     $offers = $query->get();
        // } else {
        //     $offers = $query->paginate($request->get('data', 10));
        // }

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Offers fetched successfully',
        //     'data' => $offers
        // ]);

             $sortColumn = $request->get('sort', 'id');
        $sortDirection = $request->get('direction', 'desc');
        $Courses = Course::all();

        $course = $request->get('course');

        $sortableColumns = [
            'id' => 'id',
            'name' => 'name',
            'status' => 'status',
            'discount' => 'discount',
            'course' => 'course',
            'valid_from' => 'valid_from',
            'valid_to' => 'valid_to',
        ];

        $query = Offer::query();

        // Filter by course if provided
        if ($course) {
            $query->where('course', $course);
        }

        // Sort
        if (array_key_exists($sortColumn, $sortableColumns)) {
            $query->orderBy($sortableColumns[$sortColumn], $sortDirection);
        }

        $offers = $request->data == 'all' ? $query->get() : $query->paginate($request->data);

          return response()->json([
            'success' => true,
            'message' => 'Offers fetched successfully',
            'data' => $offers
        ]);
    }

    public function destroy(string $id)
    {
        Offer::destroy($id);

        session()->flash('success', 'Offer Successfully Deleted');

        return redirect()->route('offers.index');
    }
}
