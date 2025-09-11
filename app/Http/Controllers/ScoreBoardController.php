<?php

namespace App\Http\Controllers;

use App\Models\ScoreBoard;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Language;
use App\Models\SubCategory;
use App\Models\QuizePractice;
use App\Models\QuestionBankCount;
use App\Models\MockTest;

class ScoreBoardController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/scoreboards",
     *     summary="Create a new scoreboard entry",
     *     description="Stores a new scoreboard record for a user",
     *     operationId="storeScoreboard",
     *     tags={"ScoreBoard"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"google_user_id","sub_category_id","total_videos","quiz_practice","test_rank"},
     *             @OA\Property(property="google_user_id", type="integer", example=1, description="ID of the google user"),
     *             @OA\Property(property="sub_category_id", type="integer", example=10, description="ID of the sub category"),
     *             @OA\Property(property="total_videos", type="integer", example=5, description="Number of videos watched"),
     *             @OA\Property(property="quiz_practice", type="integer", example=3, description="Number of quizzes practiced"),
     *             @OA\Property(property="test_rank", type="integer", example=1, description="User's test rank"),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Scoreboard saved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Scoreboard saved successfully!"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=15),
     *                 @OA\Property(property="google_user_id", type="integer", example=1),
     *                 @OA\Property(property="sub_category_id", type="integer", example=10),
     *                 @OA\Property(property="total_videos", type="integer", example=5),
     *                 @OA\Property(property="quiz_practice", type="integer", example=3),
     *                 @OA\Property(property="test_rank", type="integer", example=1),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-05T12:34:56Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-05T12:34:56Z"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="google_user_id", type="array",
     *                     @OA\Items(type="string", example="The selected google user id is invalid.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'google_user_id' => 'required|exists:google_users,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'total_videos' => 'required|integer|min:0',
            'quiz_practice' => 'required|integer|min:0',
            'test_rank' => 'required|integer|min:0',
        ]);

        $scoreboard = ScoreBoard::Create($request->all());

        return response()->json(['message' => 'Scoreboard saved successfully!', 'data' => $scoreboard], 200);
    }

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
     *     path="/api/scoreboards/{userId}",
     *     summary="Get a user's scoreboard",
     *     description="Retrieve scoreboard data by Google user ID with related user and sub-category details.",
     *     operationId="getUserScoreboard",
     *     tags={"ScoreBoard"},
     *
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="Google user ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Scoreboard retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Scoreboard retrieved successfully!"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=15),
     *                     @OA\Property(property="google_user_id", type="integer", example=1),
     *                     @OA\Property(property="sub_category_id", type="integer", example=10),
     *                     @OA\Property(property="total_videos", type="integer", example=5),
     *                     @OA\Property(property="quiz_practice", type="integer", example=3),
     *                     @OA\Property(property="test_rank", type="integer", example=1),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-05T12:34:56Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-05T12:34:56Z"),
     *                     @OA\Property(
     *                         property="user",
     *                         type="object",
     *                         description="Associated Google user",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="John Doe"),
     *                         @OA\Property(property="email", type="string", example="john@example.com"),
     *                     ),
     *                     @OA\Property(
     *                         property="subCategory",
     *                         type="object",
     *                         description="Associated sub-category",
     *                         @OA\Property(property="id", type="integer", example=10),
     *                         @OA\Property(property="name", type="string", example="Mathematics"),
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="No scoreboard found for this user",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No scoreboard found for this user.")
     *         )
     *     )
     * )
     */
    public function show($userId)
    {
        $scoreboard = ScoreBoard::where('google_user_id', $userId)
            ->with(['user', 'subCategory'])
            ->get();

        if ($scoreboard->isEmpty()) {
            return response()->json(['message' => 'No scoreboard found for this user.'], 404);
        }

        return response()->json(['message' => 'Scoreboard retrieved successfully!', 'data' => $scoreboard], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/quize-practice/store",
     *     summary="Store a new quiz attempt",
     *     description="Saves a quiz attempt for a Google user with subject, topic, percentage, and attempt count.",
     *     tags={"Quize"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"google_user_id","subject_id","topic_id","percentage","attempt"},
     *             @OA\Property(property="google_user_id", type="integer", example=1, description="ID of the Google user"),
     *             @OA\Property(property="subject_id", type="integer", example=2, description="ID of the subject"),
     *             @OA\Property(property="topic_id", type="integer", example=5, description="ID of the topic"),
     *             @OA\Property(property="percentage", type="number", format="float", example=85.5, description="Score percentage"),
     *             @OA\Property(property="attempt", type="integer", example=1, description="Attempt number")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Quiz attempt saved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Quiz attempt saved successfully!"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function quizeStore(Request $request)
    {
        $request->validate([
            'google_user_id' => 'required|exists:google_users,id',
            'subject_id' => 'required|exists:subjects,id',
            'topic_id' => 'required|exists:topics,id',
            'percentage' => 'required|numeric|min:0|max:100',
        ]);

        $quizExist = QuizePractice::where('google_user_id', request()->google_user_id)
            ->where('subject_id', request()->subject_id)
            ->where('topic_id', request()->topic_id)
            ->orderBy('id', 'desc')
            ->first();

        $data = [
            'google_user_id' => request()->google_user_id,
            'subject_id' => request()->subject_id,
            'topic_id' => request()->topic_id,
            'percentage' => request()->percentage,
            'attempt' => $quizExist ? $quizExist->attempt++ : 1,
        ];

        $quiz = QuizePractice::create($data);

        return response()->json([
            'message' => 'Quiz attempt saved successfully!',
            'data' => $quiz
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/quize/show/{googleUserId}",
     *     summary="Get quiz attempts by Google User ID",
     *     description="Retrieve all quiz attempts for a specific Google user with related subject and topic details.",
     *     tags={"Quize"},
     *     @OA\Parameter(
     *         name="googleUserId",
     *         in="path",
     *         required=true,
     *         description="The ID of the Google user",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Quiz attempts retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Quiz attempts retrieved successfully!"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No quiz attempt found for this user"
     *     )
     * )
     */
    public function quizeShow($googleUserId)
    {
        $quiz = QuizePractice::where('google_user_id', $googleUserId)
            ->with(['user', 'subjects', 'topic'])
            ->get();

        if ($quiz->isEmpty()) {
            return response()->json([
                'message' => 'No quiz attempt found for this user.'
            ], 404);
        }

        return response()->json([
            'message' => 'Quiz attempts retrieved successfully!',
            'data' => $quiz
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/question-bank-count/store",
     *     summary="Create or update question bank count",
     *     description="Stores or updates the count of questions solved by a user for a specific subject and topic.",
     *     operationId="questionCountStore",
     *     tags={"Question Bank"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"google_user_id", "subject_id", "topic_id", "count"},
     *             @OA\Property(property="google_user_id", type="integer", example=1, description="ID of the Google user"),
     *             @OA\Property(property="subject_id", type="integer", example=2, description="ID of the subject"),
     *             @OA\Property(property="topic_id", type="integer", example=5, description="ID of the topic"),
     *             @OA\Property(property="count", type="integer", example=10, description="Number of questions attempted")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Question bank count stored successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Question bank count created successfully!"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=12),
     *                 @OA\Property(property="google_user_id", type="integer", example=1),
     *                 @OA\Property(property="subject_id", type="integer", example=2),
     *                 @OA\Property(property="topic_id", type="integer", example=5),
     *                 @OA\Property(property="count", type="integer", example=10),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-05T12:30:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-05T12:30:00Z")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="google_user_id", type="array", @OA\Items(type="string", example="The selected google user id is invalid.")),
     *                 @OA\Property(property="subject_id", type="array", @OA\Items(type="string", example="The selected subject id is invalid.")),
     *                 @OA\Property(property="topic_id", type="array", @OA\Items(type="string", example="The selected topic id is invalid.")),
     *                 @OA\Property(property="count", type="array", @OA\Items(type="string", example="The count must be at least 0."))
     *             )
     *         )
     *     )
     * )
     */
    public function questioncountstore(Request $request)
    {
        $request->validate([
            'google_user_id' => 'required|exists:google_users,id',
            'subject_id'     => 'required|exists:subjects,id',
            'topic_id'       => 'required|exists:topics,id',
        ]);

        $today = now()->toDateString();

        $questionBank = QuestionBankCount::where('google_user_id', $request->google_user_id)
            ->where('subject_id', $request->subject_id)
            ->where('topic_id', $request->topic_id)
            ->whereDate('created_at', $today)
            ->first();

        if ($questionBank) {
         
            $questionBank->increment('count', 1);
            $message = 'Question bank Updated successfully!';
        } else {
            // Agar record nahi mila to naya record banao with count = 1
            $questionBank = QuestionBankCount::create([
                'google_user_id' => $request->google_user_id,
                'subject_id'     => $request->subject_id,
                'topic_id'       => $request->topic_id,
                'count'          => 1,
            ]);
            $message = 'Question bank created successfully!';
        }

        return response()->json([
            'message' => $message,
            'data' => $questionBank
        ], 200);
    }

public function questioncountshow($googleUserId)
{

    $dates = collect(range(6, 0))->map(function ($i) {
        return now()->subDays($i)->toDateString();
    });

    $records = QuestionBankCount::where('google_user_id', $googleUserId)
        ->whereDate('created_at', '>=', $dates->first()) 
        ->get();

    if ($records->isEmpty()) {
        return response()->json([
            'message' => 'No question bank records found for this user in the last 7 days.',
            'labels' => $dates,
            'series' => []
        ], 200);
    }

    $grouped = $records->groupBy('subject_id');

    $series = $grouped->map(function ($items, $subjectId) use ($dates) {
        $data = $dates->map(function ($date) use ($items) {

            return $items->whereBetween('created_at', [
                $date . " 00:00:00",
                $date . " 23:59:59"
            ])->sum('count');
        });

        return [
            'subject_id' => (int) $subjectId,
            'data' => $data
        ];
    })->values();

    return response()->json([
        'message' => 'Question bank records retrieved successfully!',
        'labels' => $dates,
        'series' => $series
    ], 200);
}


    /**
     * @OA\Post(
     *     path="/api/mock-test/store",
     *     summary="Create a mock test record",
     *     description="Store a new mock test record for a user with details like right/wrong answers, attempts, and time taken.",
     *     operationId="mockTestStore",
     *     tags={"Mock Test"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"google_user_id", "sub_category_id", "right_answer", "wrong_answer", "attempt", "time_taken"},
     *             @OA\Property(property="google_user_id", type="integer", example=1),
     *             @OA\Property(property="sub_category_id", type="integer", example=10),
     *             @OA\Property(property="right_answer", type="integer", example=15),
     *             @OA\Property(property="wrong_answer", type="integer", example=5),
     *             @OA\Property(property="attempt", type="integer", example=20),
     *             @OA\Property(property="time_taken", type="integer", example=1200, description="Time taken in seconds")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Mock test created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Mock test saved successfully!"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="google_user_id", type="integer", example=1),
     *                 @OA\Property(property="sub_category_id", type="integer", example=10),
     *                 @OA\Property(property="right_answer", type="integer", example=15),
     *                 @OA\Property(property="wrong_answer", type="integer", example=5),
     *                 @OA\Property(property="attempt", type="integer", example=20),
     *                 @OA\Property(property="time_taken", type="integer", example=1200)
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function mockTestStore(Request $request)
    {
        $request->validate([
            'google_user_id' => 'required|exists:google_users,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'right_answer'   => 'required|integer|min:0',
            'wrong_answer'   => 'required|integer|min:0',
            'time_taken'     => 'required|integer|min:0',
        ]);

        $testExist = MockTest::where('google_user_id', request()->google_user_id)
            ->where('sub_category_id', request()->sub_category_id)
            ->orderBy('id', 'desc')
            ->first();

        $data = [
            'google_user_id' => request()->google_user_id,
            'sub_category_id' => request()->sub_category_id,
            'right_answer' => request()->right_answer,
            'wrong_answer' => request()->wrong_answer,
            'time_taken'   => request()->time_taken,
            'attempt' => $testExist ? $testExist->attempt++ : 1,
        ];

        $mock = MockTest::create($data);

        return response()->json([
            'message' => 'Mock test saved successfully!',
            'data' => $mock
        ], 200);
    }


    /**
     * @OA\Get(
     *     path="/api/mock-test/{google_user_id}",
     *     summary="Get mock test records by user",
     *     description="Retrieve all mock test records for a given user, including related user and subcategory.",
     *     operationId="mockTestShow",
     *     tags={"Mock Test"},
     *
     *     @OA\Parameter(
     *         name="google_user_id",
     *         in="path",
     *         required=true,
     *         description="ID of the Google user",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Mock test records retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Mock test records retrieved successfully!"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="google_user_id", type="integer", example=1),
     *                     @OA\Property(property="sub_category_id", type="integer", example=10),
     *                     @OA\Property(property="right_answer", type="integer", example=15),
     *                     @OA\Property(property="wrong_answer", type="integer", example=5),
     *                     @OA\Property(property="attempt", type="integer", example=20),
     *                     @OA\Property(property="time_taken", type="integer", example=1200)
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="No mock test records found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No mock test records found for this user.")
     *         )
     *     )
     * )
     */
    public function mockTestShow($googleUserId)
    {
        $records = MockTest::where('google_user_id', $googleUserId)
            ->with(['user', 'subCategory'])
            ->get();

        if ($records->isEmpty()) {
            return response()->json([
                'message' => 'No mock test records found for this user.'
            ], 404);
        }

        return response()->json([
            'message' => 'Mock test records retrieved successfully!',
            'data' => $records
        ], 200);
    }
}
