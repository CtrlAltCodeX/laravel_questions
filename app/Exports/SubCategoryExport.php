<?php

namespace App\Exports;

use App\Models\SubCategory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SubCategoryExport implements FromCollection, WithHeadings
{
    protected $languageId;
    protected $categoryId;

    // Accept language_id and category_id as constructor arguments
    public function __construct($languageId = null, $categoryId = null)
    {
        $this->languageId = $languageId;
        $this->categoryId = $categoryId;
    }

    public function collection()
    {
        $query = SubCategory::query();

        // Apply category_id filter if provided
        if (!is_null($this->categoryId)) {
            $query->where('category_id', $this->categoryId);
        }

        // Apply language_id filter through related categories
        if (!is_null($this->languageId)) {
            $query->whereHas('category', function ($query) {
                $query->where('language_id', $this->languageId);
            });
        }

        // Fetch and map the filtered data
        return $query->get()->map(function ($SubCategory) {
            return [
                'id' => $SubCategory->id,
                'name' => $SubCategory->name,
                'category_id' => $SubCategory->category_id,
                'photo' => explode('/', $SubCategory->photo)[1] ?? $SubCategory->photo,
            ];
        });
    }

    public function headings(): array
    {
        return ['id', 'name', 'category_id', 'photo'];
    }
}
