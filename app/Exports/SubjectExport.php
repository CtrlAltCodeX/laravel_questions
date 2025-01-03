<?php

namespace App\Exports;

use App\Models\Subject;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
class SubjectExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Subject::all()->map(function ($Subject) {
            return [
                'id' => $Subject->id,
                'name' => $Subject->name,
                'sub_category_id' => $Subject->sub_category_id, 
               'photo' => $Subject->photo ? asset('storage/' . $Subject->photo) : '',
            ];
        });
    }

    public function headings(): array
    {
        return ['id', 'name','sub_category_id', 'photo'];
    }
}
