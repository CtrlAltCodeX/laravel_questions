<?php

namespace App\Exports;

use App\Models\Topic;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
class topicsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Topic::all()->map(function ($Topic) {
            return [
                'id' => $Topic->id,
                'name' => $Topic->name,
                'subject_id' => $Topic->subject_id, 
               'photo' => $Topic->photo ? asset('storage/' . $Topic->photo) : '',
            ];
        });
    }

    public function headings(): array
    {
        return ['id', 'name','subject_id', 'photo'];
    }
}
