<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Topic;
use App\Models\Subject;
use App\Models\Video;
use App\Models\Category;
use App\Models\GoogleUser;
use App\Models\Language;
use App\Models\SubCategory;
use App\Models\Question;
use Illuminate\Http\UploadedFile;

class ReportController extends Controller
{
    //  for web
    public function webindex()
    {
        $languages = Language::all();
        $categories = Category::all();
        $subcategories = SubCategory::all();
        $subjects = Subject::all();
        $topics = Topic::all();

        $reports = Report::paginate(10);
        return view('reports.index', compact(
            'reports',
            'categories',
            'subcategories',
            'subjects',
            'languages',
            'topics',
        ));
    }

    /**
     * @OA\Post(
     *     path="/api/reports",
     *     summary="Create a new report",
     *     tags={"Reports"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "title", "type", "message", "date"},
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="type", type="string", enum={"Video", "Question"}),
     *             @OA\Property(property="question_id", type="integer"),
     *             @OA\Property(property="video_id", type="integer"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="date", type="string", format="date")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Report created successfully"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'title' => 'required|string',
            'type' => 'required|in:Video,Question',
            'question_id' => 'nullable|integer',
            'video_id' => 'nullable|integer',
            'message' => 'required|string',
            'date' => 'required|date'
        ]);

        if (!$user = GoogleUser::find($request->user_id)) return response()->json(['success' => false, 'message' => 'User not Found'], 404);

        $report = Report::create($request->all());
        return response()->json(['success' => true, 'message' => 'Report added successfully', 'data' => $report], 201);
    }

    public function edit(Request $request)
    {
        $type = $request->query('type');
        $id = $request->query('id');

        if (!$id) return response()->json(['error' => 'ID is required'], 400);

        if ($type === 'Video') {
            $data = Video::with(['topic.subject.subCategory.category.language'])->find($id);
        } elseif ($type === 'Question') {
            $data = Question::find($id);
        } else {
            return response()->json(['error' => 'Invalid type'], 400);
        }

        if (!$data) return response()->json(['error' => 'Data not found'], 404);

        return response()->json(['success' => true, 'data' => $data], 200);
    }

    public function updateVideo(Request $request, $videoId, $id)
    {
        request()->validate([
            'name' => 'required',
            'topic_id' => 'required'
        ]);

        $video =  Video::find($videoId);
        $video->update(request()->all());

        if ($request->hasFile('thumbnail')) {
            $fileName = "thumbnail/" . time() . "_photo.jpg";
            $request->file('thumbnail')->storePubliclyAs('public', $fileName);
            $video->thumbnail = $fileName;
        }

        $video->save();

        $report = Report::find($id);

        if (!$report) return response()->json(['success' => false, 'message' => 'Report not found'], 404);

        $report->delete();

        return response()->json(['success' => true, 'message' => 'Video Updated and Report removed successfully', 'Video' => $video]);
    }

    public function updateQuestion(Request $request, $questionId, $id)
    {
        $rules = [];

        foreach ($request->input('module') as $moduleKey => $moduleValues) {
            $rules['module.' . $moduleKey] = 'required|array|min:1';
        }

        $request->validate($rules);

        $request->validate([
            'question' => 'required',
            'option_a' => 'required',
            'option_b' => 'required',
            'option_c' => 'required',
            'option_d' => 'required',
            'answer' => 'required',
        ], [
            'question.required' => 'The question field is required.',
            'option_a.required' => 'The option a field is required.',
            'option_b.required' => 'The option b field is required.',
            'option_c.required' => 'The option c field is required.',
            'option_d.required' => 'The option d field is required.',
            'answer.required' => 'The answer field is required.',
        ]);

        $data = $request->all();

        $question = Question::findOrFail($data['id']);

        $profileImage = null;
        if ($file = $data['photo']) {
            if ($file instanceof UploadedFile) {
                $profileImage = time() . "." . $file->getClientOriginalExtension();
                $file->move('storage/questions/', $profileImage);
                $data['photo'] = $profileImage;
            } else {
                !empty($file) ? $profileImage = $file : $profileImage = null;
            }
        }

        Question::updateOrCreate(
            [
                'id' => $data['id'],
            ],
            [
                'id' => $data['id'],
                'question_number' => $data['qno'][0],
                'question' => $data['question'][0],
                'photo' => ($data['photo'] != 'null') ? $data['photo'] : null,
                'photo_link' => $data['photo_link'] ?? null,
                'notes' => $data['notes'][0],
                'level' => $data['level'],
                'option_a' => $data['option_a'],
                'option_b' => $data['option_b'],
                'option_c' => $data['option_c'],
                'option_d' => $data['option_d'],
                'answer' => $data['answer'],
                'language_id' => $data['module']['select_language'][0],
                'category_id' => $data['module']['select_category'][0],
                'sub_category_id' => $data['module']['select_sub_category'][0],
                'subject_id' => $data['module']['select_subject'][0],
                'topic_id' => $data['module']['select_topic'][0],
                'question_bank_id' => null
            ]
        );

        $report = Report::find($id);
        if (!$report) return response()->json(['success' => false, 'message' => 'Report not found'], 404);

        $report->delete();

        return response()->json(['success' => true, 'message' => 'Question Updated and Report removed successfully',]);
    }

    //  3. Update & Remove Report from Listing
    public function update(Request $request, $id)
    {
        // $report = Report::find($id);
        // if (!$report) {
        //     return response()->json(['success' => false, 'message' => 'Report not found'], 404);
        // }

        // $report->delete();
        // return response()->json(['success' => true, 'message' => 'Report removed successfully'], 200);
    }

    public function destroy(string $id)
    {
        $report = Report::find($id);

        if (!$report) return response()->json(['success' => false, 'message' => 'Report not found'], 404);

        $report->delete();

        return response()->json(['success' => true, 'message' => 'Report deleted successfully'], 200);
    }
}
