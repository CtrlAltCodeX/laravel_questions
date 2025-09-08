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
