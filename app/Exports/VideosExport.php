<?php

namespace App\Exports;

use App\Models\Video;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VideosExport implements FromCollection, WithHeadings
{
    protected $languageId;
    protected $categoryId;
    protected $subCategoryId;
    protected $subjectId;
    protected $topicId;

    public function __construct($languageId = null, $categoryId = null, $subCategoryId = null, $subjectId = null, $topicId = null)
    {
        $this->languageId = $languageId;
        $this->categoryId = $categoryId;
        $this->subCategoryId = $subCategoryId;
        $this->subjectId = $subjectId;
        $this->topicId = $topicId;
    }

    public function collection()
    {
        $query = Video::query();

        // Filter by topic_id
        if (!is_null($this->topicId)) {
            $query->where('topic_id', $this->topicId);
        }

        // Filter by subject_id
        if (!is_null($this->subjectId)) {
            $query->whereHas('topic', function ($query) {
                $query->where('subject_id', $this->subjectId);
            });
        }

        // Filter by sub_category_id
        if (!is_null($this->subCategoryId)) {
            $query->whereHas('topic.subject', function ($query) {
                $query->where('sub_category_id', $this->subCategoryId);
            });
        }

        // Filter by category_id
        if (!is_null($this->categoryId)) {
            $query->whereHas('topic.subject.subCategory', function ($query) {
                $query->where('category_id', $this->categoryId);
            });
        }

        // Filter by language_id
        if (!is_null($this->languageId)) {
            $query->whereHas('topic.subject.subCategory.category', function ($query) {
                $query->where('language_id', $this->languageId);
            });
        }

        return $query->get()->map(function ($video) {
            return [
                'id' => $video->id,
                'name' => $video->name,
                'v_no' => $video->v_no,
                'thumbnail' => explode('/', $video->thumbnail)[1] ?? $video->thumbnail,
                'topic_id' => $video->topic_id,
                'description' => $video->description,
                'youtube_link' => $video->youtube_link,
                'video_id' => $video->video_id,
                'video_type' => $video->video_type,
                'pdf_link' => $video->pdf_link,
            ];
        });
    }

    public function headings(): array
    {
        return ['id', 'name', 'v_no', 'thumbnail', 'topic_id', 'description',  'youtube_link', 'video_id',  'video_type', 'pdf_link'];
    }
}

