<?php

namespace App\Exports;

use App\Models\topics;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
class SampletopicsExport implements FromArray, WithHeadings
{
    public function array(): array
    {
      
        return [];
    }

    public function headings(): array
    {
      
        return ['id', 'name', 'subject_id', 'photo'];
    }
}
