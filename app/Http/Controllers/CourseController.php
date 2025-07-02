<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\GoogleUser;
use App\Models\UserCourse;

use App\Models\Category;
use App\Models\Language;
use App\Models\SubCategory;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Offer;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{

    public function index(Request $request)
    {
        // Fetch all languages, categories, subcategories, subjects for filters/dropdowns
        $languages = Language::all();
        $categories = Category::all();
        $subcategories = SubCategory::all();
        $subjects = Subject::all();

        // Get filters from request
        $subject_id = $request->get('subject_id');
        $subcategory_id = $request->get('sub_category_id');
        $category_id = $request->get('category_id');
        $language_id = $request->get('language_id');

        // Sorting options from request or default
        $sortColumn = $request->get('sort', 'courses.id');
        $sortDirection = $request->get('direction', 'desc');

        // Base query with joins for related names
        $query = Course::select(
            'courses.*',
            'categories.name as category_name',
            'languages.name as language_name'
        )
            ->leftJoin('categories', 'courses.category_id', '=', 'categories.id')
            ->leftJoin('languages', 'courses.language_id', '=', 'languages.id');

        // Apply filters - assuming you want to filter by exact match on these fields
        if ($subject_id) {
            $query->whereJsonContains('subject_id', $subject_id);
        }
        if ($subcategory_id) {
            $query->whereJsonContains('sub_category_id', $subcategory_id);
        }
        if ($category_id) {
            $query->where('category_id', $category_id);
        }
        if ($language_id) {
            $query->where('language_id', $language_id);
        }

        // Define sortable columns mapping
        $sortableColumns = [
            'id' => 'courses.id',
            'name' => 'courses.name',
            'language' => 'languages.name',
            'category' => 'categories.name',
        ];


        if (array_key_exists($sortColumn, $sortableColumns)) {
            $query->orderBy($sortableColumns[$sortColumn], $sortDirection);
        }


        $courses = $request->data == 'all' ? $query->get() : $query->paginate($request->data);

        $allSubcategories = SubCategory::pluck('name', 'id')->toArray();
        $allSubjects = Subject::pluck('name', 'id')->toArray();


        // foreach ($courses as $course) {

        //     $subCategoryIds = json_decode($course->sub_category_id, true) ?? [];
        //     $subjectIds = json_decode($course->subject_id, true) ?? [];

        //     $subCategoryIds = array_filter($subCategoryIds, fn($id) => $id !== 'all');
        //     $subjectIds = array_filter($subjectIds, fn($id) => $id !== 'all');

        //     $subCategoryNames = array_filter(array_map(fn($id) => $allSubcategories[$id] ?? null, $subCategoryIds));
        //     $subjectNames = array_filter(array_map(fn($id) => $allSubjects[$id] ?? null, $subjectIds));

        //     $course->sub_category_names = implode(', ', $subCategoryNames);
        //     $course->subject_names = implode(', ', $subjectNames);
        // }


        $dropdown_list = [
            'Select Language' => $languages,
            'Select Category' => $categories,
            'Select Sub Category' => $subcategories ?? [],
            'Select Subject' => $subjects ?? [],
        ];
        foreach ($courses as $course) {
            // Decode JSON strings safely
            $subCategoryIds = json_decode($course->sub_category_id, true) ?? [];
            $subjectIds = json_decode($course->subject_id, true) ?? [];

            $subCategoryIds = array_filter($subCategoryIds, fn($id) => $id !== 'all');
            $subjectIds = array_filter($subjectIds, fn($id) => $id !== 'all');

            $subCategoryNames = array_filter(array_map(fn($id) => $allSubcategories[$id] ?? null, $subCategoryIds));
            $subjectNames = array_filter(array_map(fn($id) => $allSubjects[$id] ?? null, $subjectIds));

            $course->sub_category_names = implode(', ', $subCategoryNames);
            $course->subject_names = implode(', ', $subjectNames);

            $course->topics_count = Topic::whereIn('subject_id', $subjectIds)->count();


            if (is_string($course->subscription)) {
                $subscriptionData = json_decode($course->subscription, true);
            } elseif (is_array($course->subscription)) {
                $subscriptionData = $course->subscription;
            } else {
                $subscriptionData = [];
            }

            if (is_array($subscriptionData)) {
                $prices = [];
                $names = [];

                foreach (['monthly', 'semi_annual', 'annual'] as $type) {
                    if (isset($subscriptionData[$type])) {
                        $amount = $subscriptionData[$type]['amount'] ?? null;

                        if (!empty($amount)) {
                            $prices[] = $amount;
                            $names[] = ucfirst(str_replace('_', ' ', $type));
                        }
                    }
                }

                $course->formatted_prices = !empty($prices) ? implode('/', $prices) : '-';
                $course->subscription_names = !empty($names) ? implode(', ', $names) : '-';
            } else {
                $course->formatted_prices = '-';
                $course->subscription_names = '-';
            }
        }


        // Return view with all required data
        return view('courses.index', compact(
            'courses',
            'categories',
            'subcategories',
            'subjects',
            'languages',
            'subject_id',
            'subcategory_id',
            'category_id',
            'language_id',
            'sortColumn',
            'sortDirection',
            'dropdown_list'
        ));
    }


    public function store(Request $request)
    {

        // echo "<pre>";
        // print_r($request->all());die;
        $request->validate([
            'name' => 'required|string',
            'language_id' => 'required|integer',
            'category_id' => 'required|integer',
            'subcategories' => 'required|array',
            'subjects' => 'required|array',
            'status' => 'required|boolean',

        ]);
        $subscriptions = [];

        foreach (['monthly', 'semi_annual', 'annual'] as $type) {
            if (isset($request->subscription[$type]['active'])) {
                $subscriptions[$type] = [
                    'amount' => $request->subscription[$type]['amount'],
                    'validity' => $request->subscription[$type]['validity'],
                ];
            }
        }

        $bannerFilename = null;
        if ($request->hasFile('banner')) {
            $file = $request->file('banner');
            $bannerFilename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/courses'), $bannerFilename);
        }

        $course = Course::create([
            'name' => $request->name,
            'language_id' => $request->language_id,
            'category_id' => $request->category_id,
            'sub_category_id' => json_encode($request->subcategories),
            'subject_id' => json_encode($request->subjects),
            'status' => $request->status,
            'subscription' => $subscriptions,
            'banner' => $bannerFilename,

        ]);
        return response()->json(['success' => true, 'message' => 'Course created successfully']);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'language_id' => 'required|integer',
            'category_id' => 'required|integer',
            'subcategories' => 'required|array',
            'subjects' => 'required|array',
            'status' => 'required|boolean',
        ]);

        $course = Course::findOrFail($id);

        $subscriptions = [];
        foreach (['monthly', 'semi_annual', 'annual'] as $type) {
            if (isset($request->subscription[$type]['active'])) {
                $subscriptions[$type] = [
                    'amount' => $request->subscription[$type]['amount'],
                    'validity' => $request->subscription[$type]['validity'],
                ];
            }
        }

        // Handle banner image update
        if ($request->hasFile('banner')) {
            // Optionally delete old image
            if ($course->banner && file_exists(public_path('uploads/courses/' . $course->banner))) {
                unlink(public_path('uploads/courses/' . $course->banner));
            }

            $file = $request->file('banner');
            $bannerFilename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/courses'), $bannerFilename);
            $course->banner = $bannerFilename;
        }

        // Update course fields
        $course->name = $request->name;
        $course->language_id = $request->language_id;
        $course->category_id = $request->category_id;
        $course->sub_category_id = json_encode($request->subcategories);
        $course->subject_id = json_encode($request->subjects);
        $course->status = $request->status;
        $course->subscription = $subscriptions;

        $course->save();

        return response()->json(['success' => true, 'message' => 'Course updated successfully']);
    }


    public function destroy(string $id)
    {
        Course::destroy($id);

        session()->flash('success', 'Offer Successfully Deleted');

        return redirect()->route('courses.index');
    }

    public function getSubjects(Request $request)
    {
        $subCategoryIds = explode(',', $request->query('ids', ''));

        $subCategoryIds = array_filter($subCategoryIds);

        $subjects = Subject::whereIn('sub_category_id', $subCategoryIds)
            ->get(['id', 'name']);

        return response()->json($subjects);
    }

  
public function getCoursesWithOffers($user_id)
{
    // Step 1: Get user
    $user = GoogleUser::find($user_id);
    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'User not found.'
        ], 404);
    }

    // Step 2: Get user preferences
    $category_id = $user->category_id;
    $language_id = $user->language_id;

    // Step 3: Get user purchased courses IDs
    $purchasedCourseIds = UserCourse::where('user_id', $user_id)->pluck('course_id')->toArray();

    // Step 4: Get matching courses
    $courses = Course::where('category_id', $category_id)
        ->where('language_id', $language_id)
        ->get()
        ->map(function ($course) use ($user_id, $purchasedCourseIds) {
            
            $offer = Offer::whereJsonContains('course', (string) $course->id)
                ->latest('created_at')
                ->first();

            $courseSubscription = $course->subscription;
            $offerSubscription = $offer ? json_decode($offer->subscription, true) : [];

            foreach (['monthly', 'semi_annual', 'annual'] as $type) {
                if (isset($courseSubscription[$type]['amount'])) {
                    $amount = floatval($courseSubscription[$type]['amount']);
                    $discount = 0;

                    // Step 5: Check if user purchased this course
                    if (in_array($course->id, $purchasedCourseIds)) {
                        // Apply upgrade logic here if any
                        $discount = isset($offerSubscription[$type]['upgrade']) 
                            ? floatval($offerSubscription[$type]['upgrade']) 
                            : 0;
                    } else {
                        // Normal discount
                        $discount = isset($offerSubscription[$type]['discount']) 
                            ? floatval($offerSubscription[$type]['discount']) 
                            : 0;
                    }

                    $finalAmount = $amount - (($discount / 100) * $amount);
                    $courseSubscription[$type]['final_amount'] = round($finalAmount, 2);
                }
            }

            return [
                'id' => $course->id,
                'name' => $course->name,
                'language_id' => $course->language_id,
                'category_id' => $course->category_id,
                'sub_category_id' => $course->sub_category_id,
                'subject_id' => $course->subject_id,
                'status' => $course->status,
                'subscription' => $courseSubscription,
                'banner' => $course->banner,
                'offer' => $offer ? [
                    'id' => $offer->id,
                    'name' => $offer->name,
                    'status' => $offer->status,
                    'banner' => $offer->banner,
                    'course' => $offer->course,
                    'subscription' => $offerSubscription,
                    'valid_from' => $offer->valid_from,
                    'valid_to' => $offer->valid_to,
                ] : null,
            ];
        });

    return response()->json([
        'status' => true,
        'data' => $courses
    ]);
}

}
