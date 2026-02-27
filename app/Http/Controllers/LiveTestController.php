<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\Language;
use App\Models\LiveTest;
use App\Models\Question;
use App\Models\SubCategory;
use App\Models\Subject;
use App\Models\LiveTestManualQuestion;
use App\Exports\LiveTestManualTemplateExport;
use App\Imports\LiveTestManualImport;
use Maatwebsite\Excel\Facades\Excel;
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
            'question_ids' => 'required_if:mode,auto|array',
        ]);

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
            'question_ids' => $request->mode == 'auto' ? $request->question_ids : null,
            'status' => true,
        ]);

        if ($request->mode == 'manual' && $request->has('manual_questions')) {
            $questions = json_decode($request->manual_questions, true);
            if (is_array($questions)) {
                foreach ($questions as $q) {
                    $liveTest->manualQuestions()->create([
                        'language_id' => $request->language_id,
                        'category_id' => $q['category'] ?? ($q['category_id'] ?? null),
                        'sub_category_id' => $q['subcategory'] ?? ($q['sub_category_id'] ?? null),
                        'subject_id' => $q['subject'] ?? ($q['subject_id'] ?? null),
                        'question' => $q['question'] ?? null,
                        'option_a' => $q['option_a'] ?? null,
                        'option_b' => $q['option_b'] ?? null,
                        'option_c' => $q['option_c'] ?? null,
                        'option_d' => $q['option_d'] ?? null,
                        'answer' => $q['answer'] ?? null,
                        'photo' => $q['photo'] ?? null,
                    ]);
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Live Test created successfully']);
    }

    public function destroy($id)
    {
        LiveTest::destroy($id);
        return redirect()->route('live-tests.index')->with('success', 'Live Test deleted successfully');
    }

    public function edit($id)
    {
        $liveTest = LiveTest::with('manualQuestions')->findOrFail($id);
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
            'question_ids' => 'required_if:mode,auto|array',
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
            'question_ids' => $request->mode == 'auto' ? $request->question_ids : null,
        ]);

        if ($request->mode == 'manual' && $request->has('manual_questions')) {
            $liveTest->manualQuestions()->delete();
            $questions = json_decode($request->manual_questions, true);
            if (is_array($questions)) {
                foreach ($questions as $q) {
                    $liveTest->manualQuestions()->create([
                        'language_id' => $request->language_id,
                        'category_id' => $q['category'] ?? ($q['category_id'] ?? null),
                        'sub_category_id' => $q['subcategory'] ?? ($q['sub_category_id'] ?? null),
                        'subject_id' => $q['subject'] ?? ($q['subject_id'] ?? null),
                        'question' => $q['question'] ?? null,
                        'option_a' => $q['option_a'] ?? null,
                        'option_b' => $q['option_b'] ?? null,
                        'option_c' => $q['option_c'] ?? null,
                        'option_d' => $q['option_d'] ?? null,
                        'answer' => $q['answer'] ?? null,
                        'photo' => $q['photo'] ?? null,
                    ]);
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Live Test updated successfully']);
    }

    public function downloadManualTemplate(Request $request)
    {
        $languageId = $request->language_id;
        $categoryId = $request->category_id;
        $subCategoryId = $request->sub_category_id;

        $matchingCourses = Course::where('language_id', $languageId)
            ->where('category_id', $categoryId)
            ->get()
            ->filter(function ($c) use ($subCategoryId) {
                $courseSubs = array_map('strval', (array)$c->sub_category_id);
                return in_array(strval($subCategoryId), $courseSubs);
            });

        $templateData = [];
        $categoryName = Category::find($categoryId)?->name;
        $subCategoryName = SubCategory::find($subCategoryId)?->name;

        foreach ($matchingCourses as $course) {
            $sids = [];
            if ($course->subject_limit && is_array($course->subject_limit)) {
                $sids = array_filter(array_keys($course->subject_limit), 'is_numeric');
            } elseif ($course->subject_id) {
                $sids = (array)$course->subject_id;
            }

            foreach ($sids as $sid) {
                // Add one sample row per subject found in course
                $templateData[] = [
                    'language_id' => $languageId,
                    'category' => $categoryId,
                    'subcategory' => $subCategoryId,
                    'subject' => $sid,
                    'question' => '',
                    'option_a' => '',
                    'option_b' => '',
                    'option_c' => '',
                    'option_d' => '',
                    'answer' => '',
                    'photo' => ''
                ];
            }
        }

        if (empty($templateData)) {
            $templateData[] = [
                'language_id' => $languageId, 'category' => $categoryId, 
                'subcategory' => $subCategoryId, 'subject' => '', 'question' => '', 
                'option_a' => '', 'option_b' => '', 'option_c' => '', 'option_d' => '', 
                'answer' => '', 'photo' => ''
            ];
        }

        return Excel::download(new LiveTestManualTemplateExport($templateData), 'live_test_manual_template.xlsx');
    }

    public function previewManualData(Request $request)
    {
        if (!$request->hasFile('excel_file')) {
            return response()->json(['success' => false, 'message' => 'No file uploaded']);
        }

        try {
            $rows = Excel::toCollection(new LiveTestManualImport, $request->file('excel_file'))->first();
            return response()->json([
                'success' => true,
                'data' => $rows
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
