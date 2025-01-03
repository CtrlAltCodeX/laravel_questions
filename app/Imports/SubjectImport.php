<?php

namespace App\Imports;

use App\Models\Subject;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
class SubjectImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
   
        $Subject = Subject::find($row['id']);

        if ($Subject) {
          
            $Subject->update([
                'name' => $row['name'],
                'sub_category_id' => $row['sub_category_id'],
                'photo' => $row['photo'], 
            ]);
            return null;
        } else {
            // Create new record
            return new Subject([
                'id' => $row['id'], 
                'name' => $row['name'],
                'sub_category_id' => $row['sub_category_id'],
                'photo' => $row['photo'],
            ]);
        }
    }
}
