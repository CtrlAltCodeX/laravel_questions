<?php

namespace App\Exports;

use App\Models\Topic;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class topicsExport implements FromCollection, WithHeadings
{
    protected $languageId;
    protected $categoryId;
    protected $subCategoryId;
    protected $subjectId;

    // Constructor to accept filter parameters
    public function __construct($languageId = null, $categoryId = null, $subCategoryId = null, $subjectId = null)
    {
        $this->languageId = $languageId;
        $this->categoryId = $categoryId;
        $this->subCategoryId = $subCategoryId;
        $this->subjectId = $subjectId;
    }

    public function collection()
    {
        $query = Topic::query();

        // Apply subject_id filter if provided
        if (!is_null($this->subjectId)) {
            $query->where('subject_id', $this->subjectId);
        }

        // Apply sub_category_id, category_id, and language_id filters through related subjects
        if (!is_null($this->subCategoryId) || !is_null($this->categoryId) || !is_null($this->languageId)) {
            $query->whereHas('subject.subCategory.category', function ($query) {
                if (!is_null($this->subCategoryId)) {
                    $query->where('id', $this->subCategoryId);
                }
                if (!is_null($this->categoryId)) {
                    $query->where('category_id', $this->categoryId);
                }
                if (!is_null($this->languageId)) {
                    $query->where('language_id', $this->languageId);
                }
            });
        }

        // Fetch and map the filtered data
        return $query->get()->map(function ($Topic) {
            return [
                'id' => $Topic->id,
                'name' => $Topic->name,
                'subject_id' => $Topic->subject_id,
                'photo' => $Topic->photo ? asset('storage/' . $Topic->photo) : '',
            ];
        });
    }

    public function headings(): array
    {
        return ['id', 'name', 'subject_id', 'photo'];
    }
}
