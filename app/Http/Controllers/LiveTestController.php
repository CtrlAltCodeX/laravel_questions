<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\Language;
use App\Models\LiveTest;
use App\Models\Question;
use App\Models\SubCategory;
use App\Models\Subject;
use Illuminate\Http\Request;

class LiveTestController extends Controller
{
    public function index()
    {
        $languages = Language::all();
        $categories = Category::all();
        $liveTests = LiveTest::with(['language', 'category'])->latest()->paginate(10);
        
        return view('live_tests.index', compact('languages', 'categories', 'liveTests'));
    }

    public function getQuestions(Request $request)
    {
        $languageId = $request->language_id;
        $categoryId = $request->category_id;
        $subCategoryId = $request->sub_category_id; // Changed from sub_category_ids to sub_category_id

        if (!$languageId || !$categoryId || !$subCategoryId) {
            return response()->json(['subjects' => [], 'subject_count' => 0, 'question_count' => 0]);
        }

        $subCategoryIds = (array)$subCategoryId; // Convert to array for matching logic

        // Get matching course to find question limit
        $allCourses = Course::where('language_id', $languageId)
            ->where('category_id', $categoryId)
            ->get();

        $matchingCourses = $allCourses->filter(function ($c) use ($subCategoryIds) {
                $courseSubs = array_map('strval', (array)$c->sub_category_id);
                $inputSubs = array_map('strval', (array)$subCategoryIds);
                return count(array_intersect($courseSubs, $inputSubs)) > 0;
            });

        $subjectsWithQuestions = [];
        $totalQuestionsCount = 0;
        $processedSubjectIds = [];
        $globalLimit = 0;

        foreach ($matchingCourses as $course) {
            // Determine which subjects this course record applies to
            $sids = [];
            if ($course->subject_limit) {
                $sids = array_keys((array)$course->subject_limit);
            } elseif ($course->subject_id) {
                $sids = (array)$course->subject_id;
            }

            foreach ($sids as $sid) {
                // Skip if we already processed this subject (avoid duplicates)
                if (in_array($sid, $processedSubjectIds)) continue;

                $subject = Subject::find($sid);
                if (!$subject) continue;

                // Determine limit for this specific subject
                $subjectLimit = 0;
                if ($course->subject_limit && isset($course->subject_limit[$sid])) {
                    $subjectLimit = (int)$course->subject_limit[$sid];
                } else {
                    $subjectLimit = (int)$course->question_limit;
                }

                if ($subjectLimit <= 0) $subjectLimit = (int)$course->question_limit ?: 50;

                $questions = Question::where('language_id', $languageId)
                    ->where('category_id', $categoryId)
                    ->where('subject_id', $sid)
                    ->get();

                if ($questions->count() > 0) {
                    $subjectsWithQuestions[] = [
                        'id' => $subject->id,
                        'name' => $subject->name,
                        'questions' => $questions,
                        'limit' => $subjectLimit
                    ];
                    $totalQuestionsCount += $questions->count();
                    $processedSubjectIds[] = $sid;
                }
            }
            
            if ($course->question_limit > $globalLimit) {
                $globalLimit = (int)$course->question_limit;
            }
        }

        return response()->json([
            'subjects' => $subjectsWithQuestions,
            'subject_count' => count($subjectsWithQuestions),
            'question_count' => $totalQuestionsCount,
            'limit' => $globalLimit ?: "NO LIMIT"
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'language_id' => 'required|exists:languages,id',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required', // Changed from sub_category_ids to sub_category_id
            'mode' => 'required|in:auto,manual',
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'toppers_star' => 'nullable|integer',
            'toppers' => 'nullable|integer',
            'participant_star' => 'nullable|integer',
            'question_ids' => 'required|array',
        ]);

        if ($request->mode == 'manual') {
            return response()->json(['success' => false, 'message' => 'Manual mode coming soon']);
        }

        $liveTest = LiveTest::create([
            'language_id' => $request->language_id,
            'category_id' => $request->category_id,
            'sub_category_id' => (array)$request->sub_category_id,
            'mode' => $request->mode,
            'title' => $request->title,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'toppers_star' => $request->toppers_star,
            'toppers' => $request->toppers,
            'participant_star' => $request->participant_star,
            'question_ids' => $request->question_ids,
            'status' => true,
        ]);

        return response()->json(['success' => true, 'message' => 'Live Test created successfully']);
    }

    public function destroy($id)
    {
        LiveTest::destroy($id);
        return redirect()->route('live-tests.index')->with('success', 'Live Test deleted successfully');
    }

    public function edit($id)
    {
        $liveTest = LiveTest::findOrFail($id);
        return response()->json($liveTest);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'language_id' => 'required|exists:languages,id',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required',
            'mode' => 'required|in:auto,manual',
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'toppers_star' => 'nullable|integer',
            'toppers' => 'nullable|integer',
            'participant_star' => 'nullable|integer',
            'question_ids' => 'required|array',
        ]);

        $liveTest = LiveTest::findOrFail($id);
        $liveTest->update([
            'language_id' => $request->language_id,
            'category_id' => $request->category_id,
            'sub_category_id' => (array)$request->sub_category_id,
            'mode' => $request->mode,
            'title' => $request->title,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'toppers_star' => $request->toppers_star,
            'toppers' => $request->toppers,
            'participant_star' => $request->participant_star,
            'question_ids' => $request->question_ids,
        ]);

        return response()->json(['success' => true, 'message' => 'Live Test updated successfully']);
    }
}
