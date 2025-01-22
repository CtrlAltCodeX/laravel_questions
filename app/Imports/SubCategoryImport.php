<?php

namespace App\Imports;

use App\Models\SubCategory;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\WithValidation;

class SubCategoryImport implements ToModel, WithHeadingRow, SkipsOnError, WithValidation
{
    use SkipsErrors;

    public $validationErrors = []; 

    public function rules(): array
    {
        return [
            'id' => 'required|integer',
            'name' => 'required|string|max:255',
            'category_id' => 'required|integer',
            'photo' => 'nullable|string',
        ];
    }

    public function model(array $row)
    {   
        $subCategory = SubCategory::find($row['id']);

        if ($subCategory) {
            $subCategory->update([
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
