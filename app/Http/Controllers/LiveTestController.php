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
        $subCategoryIds = $request->sub_category_ids; // Expected as array

        if (!$languageId || !$categoryId || !$subCategoryIds) {
            return response()->json(['subjects' => [], 'subject_count' => 0, 'question_count' => 0]);
        }

        // Get matching course to find question limit
        $allCourses = Course::where('language_id', $languageId)
            ->where('category_id', $categoryId)
            ->get();

        $course = $allCourses->filter(function ($c) use ($subCategoryIds) {
                $courseSubs = array_map('strval', (array)$c->sub_category_id);
                $inputSubs = array_map('strval', (array)$subCategoryIds);
                $intersect = array_intersect($courseSubs, $inputSubs);
                return count($intersect) > 0;
            })
            ->first();

        $limit = $course ? $course->question_limit : 50;

        // Get subjects for these sub categories
        $subjects = Subject::whereIn('sub_category_id', $subCategoryIds)->get();
        
        $subjectsWithQuestions = [];
        $totalQuestionsCount = 0;

        foreach ($subjects as $subject) {
            $questions = Question::where('language_id', $languageId)
                ->where('category_id', $categoryId)
                ->where('subject_id', $subject->id)
                ->limit($limit)
                ->get();

            if ($questions->count() > 0) {
                $subjectsWithQuestions[] = [
                    'id' => $subject->id,
                    'name' => $subject->name,
                    'questions' => $questions
                ];
                $totalQuestionsCount += $questions->count();
            }
        }

        return response()->json([
            'subjects' => $subjectsWithQuestions,
            'subject_count' => count($subjectsWithQuestions),
            'question_count' => $totalQuestionsCount,
            'limit' => $limit
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'language_id' => 'required|exists:languages,id',
            'category_id' => 'required|exists:categories,id',
            'sub_category_ids' => 'required|array',
            'mode' => 'required|in:auto,manual',
            'title' => 'required|string|max:255',
            'schedule' => 'required|date',
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
            'sub_category_id' => $request->sub_category_ids,
            'mode' => $request->mode,
            'title' => $request->title,
            'schedule' => $request->schedule,
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
            'sub_category_ids' => 'required|array',
            'mode' => 'required|in:auto,manual',
            'title' => 'required|string|max:255',
            'schedule' => 'required|date',
            'toppers_star' => 'nullable|integer',
            'toppers' => 'nullable|integer',
            'participant_star' => 'nullable|integer',
            'question_ids' => 'required|array',
        ]);

        $liveTest = LiveTest::findOrFail($id);
        $liveTest->update([
            'language_id' => $request->language_id,
            'category_id' => $request->category_id,
            'sub_category_id' => $request->sub_category_ids,
            'mode' => $request->mode,
            'title' => $request->title,
            'schedule' => $request->schedule,
            'toppers_star' => $request->toppers_star,
            'toppers' => $request->toppers,
            'participant_star' => $request->participant_star,
            'question_ids' => $request->question_ids,
        ]);

        return response()->json(['success' => true, 'message' => 'Live Test updated successfully']);
    }
}
