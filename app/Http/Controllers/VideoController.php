<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Language;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Exports\VideosExport;
use App\Imports\VideosImport;
use App\Models\Topic;
use App\Models\Subject;
use App\Models\Video;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage; // ✅ Correctly placed here


class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $languages = Language::all();
        $categories = Category::all();
        $subcategories = SubCategory::all();
        $subjects = Subject::all();
        $topics = Topic::all();

        $subject_id = $request->get('subject_id');
        $subcategory_id = $request->get('sub_category_id');
        $category_id = $request->get('category_id');
        $language_id = $request->get('language_id');
        $topic_id = $request->get('topic_id');

        $sortColumn = $request->get('sort', 'videos.id');
        $sortDirection = $request->get('direction', 'desc');

        // Prepare the query
        $query = Video::select('videos.*')->with([
            'topic' => function ($q) {
                $q->with(['subject' => function ($q) {
                    $q->with(['subCategory' => function ($q) {
                        $q->with('category.language');
                    }]);
                }]);
            }
        ]);

        if ($topic_id) {
            $query->where('topic_id', $topic_id);
        }
        if ($subject_id) {
            $query->whereHas('topic.subject', function ($q) use ($subject_id) {
                $q->where('id', $subject_id);
            });
        }
        if ($subcategory_id) {
            $query->whereHas('topic.subject.subCategory', function ($q) use ($subcategory_id) {
                $q->where('id', $subcategory_id);
            });
        }
        if ($category_id) {
            $query->whereHas('topic.subject.subCategory.category', function ($q) use ($category_id) {
                $q->where('id', $category_id);
            });
        }
        if ($language_id) {
            $query->whereHas('topic.subject.subCategory.category.language', function ($q) use ($language_id) {
                $q->where('id', $language_id);
            });
        }

        // Validate sort column
        $sortableColumns = [
            'id' => 'videos.id',
            'name' => 'videos.name',
            'language' => 'languages.name',
            'category' => 'categories.name',
            'sub_category' => 'sub_categories.name',
            'subject' => 'subjects.name',
            'topic' => 'topics.name',
        ];

        if (array_key_exists($sortColumn, $sortableColumns)) {
            // Include necessary joins for sorting by related columns
            $query->join('topics', 'videos.topic_id', '=', 'topics.id')
                ->join('subjects', 'topics.subject_id', '=', 'subjects.id')
                ->join('sub_categories', 'subjects.sub_category_id', '=', 'sub_categories.id')
                ->join('categories', 'sub_categories.category_id', '=', 'categories.id')
                ->join('languages', 'categories.language_id', '=', 'languages.id');

            // Apply sorting using the proper table aliases
            $query->orderBy($sortableColumns[$sortColumn], $sortDirection);
        }

        if (request()->data == 'all') {
            $videos = $query->get();
        } else {
            $videos = $query->paginate(request()->data);
        }

        $dropdown_list = [
            'Select Language' => $languages,
            'Select Category' => $categories,
            'Select Sub Category' => $subcategories ?? [],
            'Select Subject' => $subjects ?? [],
            'Select Topic' => $topics ?? [],
        ];

        // Return the view with all necessary data
        return view('videos.index', compact(
            'videos',
            'categories',
            'subcategories',
            'subjects',
            'languages',
            'topics',
            'subject_id',
            'subcategory_id',
            'category_id',
            'language_id',
            'topic_id',
            'sortColumn',
            'sortDirection',
            'dropdown_list'
        ));
    }

    public function export(Request $request)
    {
        $languageId = $request->get('language_id');
        $categoryId = $request->get('category_id');
        $subCategoryId = $request->get('sub_category_id');
        $subjectId = $request->get('subject_id');
        $topicId = $request->get('topic_id');

        return Excel::download(new VideosExport($languageId, $categoryId, $subCategoryId,  $subjectId, $topicId), 'Videos.xlsx');
    }


    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx',
        ]);

        $importer = new VideosImport();

        try {
            $rows = Excel::import($importer, $request->file('file'));

            foreach ($rows as $key => $row) {
                $rowCount = $key + 2;

                if (empty($row['topic_id'])) {
                    return back()->with('error', 'Row: ' . $rowCount . '- topic is required.');
                }

                if (!$this->getTopicId($row['topic_id'])) {
                    return back()->with('error', 'topic - "' . $row['topic_id'] . '" not available');
                }
            }
        } catch (\Exception $e) {
            return redirect()->route('videos.index')
                ->with('import_errors', [$e->getMessage()]);
        }

        if (!empty($importer->errors)) {
            return redirect()->route('videos.index')
                ->with('import_errors', $importer->errors);
        }

        return redirect()->route('videos.index')->with('success', 'videos imported successfully!');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'topic_id' => 'required',
            'video' => 'required|file|mimes:mp4,mov,avi|max:204800', // example validation
        ]);

        $video = Video::create(array_merge($request->except(['thumbnail', 'pdf_link', 'video']), [
            'duration' => now()->format('H:i:s')
        ]));

        // Upload to MinIO
        if ($request->hasFile('thumbnail')) {
            $fileName = "thumbnail/" . time() . "_photo.jpg";
            $request->file('thumbnail')->storePubliclyAs('public', $fileName);
            $video->thumbnail = $fileName;

            // $path = request()->language_id . "/" . request()->category_id . "/" . request()->sub_category_id . "/" . request()->subject_id . "/" . request()->topic_id;
            // $fileName = $path . '/thumbnails/' . time() . '_' . $request->file('thumbnail')->getClientOriginalName();
            // Storage::disk('minio')->put($fileName, file_get_contents($request->file('thumbnail')));
            // $video->thumbnail = $fileName;
        }

        if ($request->hasFile('pdf_link')) {
            $path = request()->language_id . "/" . request()->category_id . "/" . request()->sub_category_id . "/" . request()->subject_id . "/" . request()->topic_id;

            $pdfFileName = $path . '/pdfs/' . time() . '_' . $request->file('pdf_link')->getClientOriginalName();
            Storage::disk('minio')->put($pdfFileName, file_get_contents($request->file('pdf_link')));
            $video->pdf_link = $pdfFileName;
        }

        if ($request->hasFile('video')) {
            $path = request()->language_id . "/" . request()->category_id . "/" . request()->sub_category_id . "/" . request()->subject_id . "/" . request()->topic_id;
            $videoFileName = $path . '/videos/' . time() . '_' . $request->file('video')->getClientOriginalName();
            Storage::disk('minio')->put($videoFileName, file_get_contents($request->file('video')));
        }

        $video->save();

        return response()->json([
            'success' => true,
            'message' => 'Video Successfully Created',
            'video' => $video
        ]);
    }

    public function listVideos()
    {
        $files = Storage::disk('s3')->files('videos');
        $urls = array_map(fn($file) => Storage::disk('s3')->url($file), $files);

        return response()->json($urls);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
            'topic_id' => 'required'
        ]);

        $video = Video::find($id);
        if (!$video) {
            return response()->json(['success' => false, 'message' => 'Video not found'], 404);
        }

        // Update text fields
        $video->update($request->except(['thumbnail', 'pdf_link', 'video']));

        // === Handle Thumbnail ===
        if ($request->hasFile('thumbnail')) {
            if ($video->thumbnail && Storage::disk('minio')->exists($video->thumbnail)) {
                Storage::disk('minio')->delete($video->thumbnail);
            }

            $fileName = "thumbnail/" . time() . "_photo.jpg";
            $request->file('thumbnail')->storePubliclyAs('public', $fileName);
            $video->thumbnail = $fileName;

            // $path = request()->language_id . "/" . request()->category_id . "/" . request()->sub_category_id . "/" . request()->subject_id . "/" . request()->topic_id;
            // $fileName = $path . '/thumbnails/' . time() . '_' . $request->file('thumbnail')->getClientOriginalName();
            // Storage::disk('minio')->put($fileName, file_get_contents($request->file('thumbnail')));
            // $video->thumbnail = $fileName;
        }

        // === Handle PDF ===
        if ($request->hasFile('pdf_link')) {
            // Delete old PDF if exists
            if ($video->pdf_link && Storage::disk('minio')->exists($video->pdf_link)) {
                Storage::disk('minio')->delete($video->pdf_link);
            }

            $path = request()->language_id . "/" . request()->category_id . "/" . request()->sub_category_id . "/" . request()->subject_id . "/" . request()->topic_id;
            $pdfFileName = $path . 'pdfs/' . time() . '_' . $request->file('pdf_link')->getClientOriginalName();
            Storage::disk('minio')->put($pdfFileName, file_get_contents($request->file('pdf_link')));
            $video->pdf_link = $pdfFileName;
        }

        // === Handle Video ===
        if ($request->hasFile('video')) {
            // Delete old video if exists
            if ($video->video && Storage::disk('minio')->exists($video->video)) {
                Storage::disk('minio')->delete($video->video);
            }

            $path = request()->language_id . "/" . request()->category_id . "/" . request()->sub_category_id . "/" . request()->subject_id . "/" . request()->topic_id;
            $videoFileName = $path . '/videos/' . time() . '_' . $request->file('video')->getClientOriginalName();
            Storage::disk('minio')->put($videoFileName, file_get_contents($request->file('video')));
        }

        $video->save();

        return response()->json([
            'success' => true,
            'message' => 'Video Successfully Updated',
            'video' => $video
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Video::destroy($id);

        session()->flash('success', 'Video Successfully Deleted');

        return redirect()->route('videos.index');
    }
}
