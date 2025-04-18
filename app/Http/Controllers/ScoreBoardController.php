<?php

namespace App\Http\Controllers;

use App\Models\ScoreBoard;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Language;
use App\Models\SubCategory;

class ScoreBoardController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/scoreboard",
     *     summary="Save or update scoreboard",
     *     tags={"ScoreBoard"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"google_user_id", "sub_category_id", "total_videos", "quiz_practice", "test_rank"},
     *             @OA\Property(property="google_user_id", type="integer", example=1),
     *             @OA\Property(property="sub_category_id", type="integer", example=2),
     *             @OA\Property(property="total_videos", type="integer", example=10),
     *             @OA\Property(property="quiz_practice", type="integer", example=5),
     *             @OA\Property(property="test_rank", type="integer", example=3)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Scoreboard saved successfully"),
     *     @OA\Response(response=400, description="Validation Error")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'google_user_id' => 'required|exists:google_users,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'total_videos' => 'required|integer|min:0',
            'quiz_practice' => 'required|integer|min:0',
            'test_rank' => 'required|integer|min:0',
        ]);

        $scoreboard = ScoreBoard::Create($request->all());

        return response()->json(['message' => 'Scoreboard saved successfully!', 'data' => $scoreboard], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/scoreboard",
     *     summary="Get scoreboard data",
     *     tags={"ScoreBoard"},
     *     @OA\Response(response=200, description="Scoreboard retrieved successfully"),
     *     @OA\Response(response=404, description="No scoreboard found")
     * )
     */
    public function index(Request $request)
    {
        $languages = Language::all();
        $categories = Category::all();
        $subcategories = SubCategory::all();

        $subcategory_id = $request->get('sub_category_id');
        $category_id = $request->get('category_id');
        $language_id = $request->get('language_id');

        $sortColumn = $request->get('sort', 'score_boards.id');
        $sortDirection = $request->get('direction', 'desc');

        $query = ScoreBoard::with('subCategory.category.language');

        if ($subcategory_id) {
            $query->where('sub_category_id', $subcategory_id);
        }
        if ($category_id) {
            $query->whereHas('subCategory', function ($q) use ($category_id) {
                $q->where('category_id', $category_id);
            });
        }
        if ($language_id) {
            $query->whereHas('subCategory.category', function ($q) use ($language_id) {
                $q->where('language_id', $language_id);
            });
        }

        $sortableColumns = ['id' => 'score_boards.id', 'sub_category' => 'sub_categories.name'];

        if (array_key_exists($sortColumn, $sortableColumns)) {
            $query->join('sub_categories', 'score_boards.sub_category_id', '=', 'sub_categories.id')
                ->orderBy($sortableColumns[$sortColumn], $sortDirection);
        }

        $ScoreBoards = request()->data == 'all' ? $query->get() : $query->paginate(request()->data ?? 10);

        return view('ScoreBoard.index', compact('ScoreBoards', 'categories', 'subcategories', 'languages', 'subcategory_id', 'category_id', 'language_id', 'sortColumn', 'sortDirection'));
    }

    /**
     * @OA\Get(
     *     path="/api/scoreboard/{userId}",
     *     summary="Get scoreboard by user ID",
     *     tags={"ScoreBoard"},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Scoreboard retrieved successfully"),
     *     @OA\Response(response=404, description="No scoreboard found for this user")
     * )
     */
    public function show($userId)
    {
        $scoreboard = ScoreBoard::where('google_user_id', $userId)->with(['user', 'subCategory'])->get();
        if ($scoreboard->isEmpty()) {
            return response()->json(['message' => 'No scoreboard found for this user.'], 404);
        }
        return response()->json($scoreboard);
    }
}
