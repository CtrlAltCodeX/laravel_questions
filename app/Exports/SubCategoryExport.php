<?php

namespace App\Exports;

use App\Models\SubCategory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
class SubCategoryExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return SubCategory::all()->map(function ($SubCategory) {
            return [
                'id' => $SubCategory->id,
                'name' => $SubCategory->name,
                'category_id' => $SubCategory->category_id, 
               'photo' => $SubCategory->photo ? asset('storage/' . $SubCategory->photo) : '',
            ];
        });
    }

    public function headings(): array
    {
        return ['id', 'name', 'category_id', 'photo'];
    }
}
