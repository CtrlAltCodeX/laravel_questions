<?php

namespace App\Exports;

use App\Models\SubCategory;
use Maatwebsite\Excel\Concerns\FromArray;

use Maatwebsite\Excel\Concerns\WithHeadings;

class SampleSubCategoryExport implements FromArray, WithHeadings
{
    public function array(): array
    {
      
        return [];
    }

    public function headings(): array
    {
      
        return ['id', 'name', 'category_id', 'photo'];
    }
}
