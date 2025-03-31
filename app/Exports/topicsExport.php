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

        // Filter by subject_id if provided
        if (!is_null($this->subjectId)) {
            $query->where('subject_id', $this->subjectId);
        }

        // Apply subCategoryId filter if provided
        if (!is_null($this->subCategoryId)) {
            $query->whereHas('subject', function ($query) {
                $query->where('sub_category_id', $this->subCategoryId);
            });
        }

        // Apply categoryId filter if provided
        if (!is_null($this->categoryId)) {
            $query->whereHas('subject.subCategory', function ($query) {
                $query->where('category_id', $this->categoryId);
            });
        }

        // Apply languageId filter if provided
        if (!is_null($this->languageId)) {
            $query->whereHas('subject.subCategory.category', function ($query) {
                $query->where('language_id', $this->languageId);
            });
        }

        // Fetch and map the filtered data
        $data = $query->get()->map(function ($Topic) {
            return [
                'id' => $Topic->id,
                'name' => $Topic->name,
                'subject_id' => $Topic->subject_id,
                'photo' => explode('/', $Topic->photo)[1] ?? $Topic->photo,
            ];
        });

        return $data; // Return the data for export
    }

    public function headings(): array
    {
        return ['id', 'name', 'subject_id', 'photo'];
    }
}
