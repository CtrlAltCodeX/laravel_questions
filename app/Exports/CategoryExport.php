<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CategoryExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Category::all()->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'language_id' => $category->language_id, 
               'photo' => $category->photo ? asset('storage/' . $category->photo) : '',
            ];
        });
    }

    public function headings(): array
    {
        return ['id', 'name', 'language_id', 'photo'];
    }
}
