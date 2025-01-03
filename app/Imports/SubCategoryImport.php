<?php

namespace App\Imports;

use App\Models\SubCategory;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SubCategoryImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
   
        $SubCategory = SubCategory::find($row['id']);

        if ($SubCategory) {
          
            $SubCategory->update([
                'name' => $row['name'],
                'category_id' => $row['category_id'],
                'photo' => $row['photo'], 
            ]);
            return null;
        } else {
            // Create new record
            return new SubCategory([
                'id' => $row['id'], 
                'name' => $row['name'],
                'category_id' => $row['category_id'],
                'photo' => $row['photo'],
            ]);
        }
    }
}
