<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CategoryExport implements FromCollection, WithHeadings
{
    protected $languageId;

    public function __construct($languageId = null)
    {
        $this->languageId = $languageId;
    }

    public function collection()
    {
        $query = Category::query();

        if (!is_null($this->languageId)) {
            $query->where('language_id', $this->languageId);
        }

        return $query->get()->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'language_id' => $category->language_id,
                'photo' => explode('/', $category->photo)[1] ?? $category->photo,
            ];
        });
    }

    public function headings(): array
    {
        return ['id', 'name', 'language_id', 'photo'];
    }
}
