<?php

namespace App\Exports;

use App\Models\Subject;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SubjectExport implements FromCollection, WithHeadings
{
    protected $languageId;
    protected $categoryId;
    protected $subCategoryId;

    // Constructor to accept filter parameters
    public function __construct($languageId = null, $categoryId = null, $subCategoryId = null)
    {
        $this->languageId = $languageId;
        $this->categoryId = $categoryId;
        $this->subCategoryId = $subCategoryId;
    }

    public function collection()
    {
        $query = Subject::query();

        // Apply sub_category_id filter if provided
        if (!is_null($this->subCategoryId)) {
            $query->where('sub_category_id', $this->subCategoryId);
        }

        // Apply category_id and language_id filters through related subcategories
        if (!is_null($this->categoryId) || !is_null($this->languageId)) {
            $query->whereHas('subCategory.category', function ($query) {
                if (!is_null($this->categoryId)) {
                    $query->where('id', $this->categoryId);
                }
                if (!is_null($this->languageId)) {
                    $query->where('language_id', $this->languageId);
                }
            });
        }

        // Fetch and map the filtered data
        return $query->get()->map(function ($Subject) {
            return [
                'id' => $Subject->id,
                'name' => $Subject->name,
                'sub_category_id' => $Subject->sub_category_id,
                'photo' => explode('/', $Subject->photo)[1] ?? $Subject->photo,
                'parent_id' => $Subject->parent_id
            ];
        });
    }

    public function headings(): array
    {
        return ['id', 'name', 'sub_category_id', 'photo', 'parent_id'];
    }
}
