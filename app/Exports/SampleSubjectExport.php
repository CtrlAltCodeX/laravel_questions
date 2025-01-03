<?php

namespace App\Exports;

use App\Models\Subject;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
class SampleSubjectExport implements FromArray, WithHeadings
{
    public function array(): array
    {
      
        return [];
    }

    public function headings(): array
    {
      
        return ['id', 'name', 'sub_category_id', 'photo'];
    }
}
