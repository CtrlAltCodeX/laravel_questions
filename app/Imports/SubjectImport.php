<?php

namespace App\Imports;

use App\Models\Subject;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\WithValidation;

class SubjectImport implements ToModel, WithHeadingRow, SkipsOnError, WithValidation
{
    use SkipsErrors;

    public $validationErrors = [];

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'sub_category_id' => 'required|integer',
            'photo' => 'nullable|string|max:255',
        ];
    }

    public function model(array $row)
    {
        $Subject = Subject::find($row['id']);
        
        if ($Subject) {
            $Subject->update([
                'name' => $row['name'],
                'sub_category_id' => $row['sub_category_id'],
                'photo' => $row['photo'] ? ("subject/" . $row['photo'] ?? null) : null,
                'parent_id' => $row['parent_id'],
            ]);

            return null;
        } else {
            // Create new record
            return new Subject([
                'id' => $row['id'],
                'name' => $row['name'],
                'sub_category_id' => $row['sub_category_id'],
                'photo' => $row['photo'] ? ("subject/" . $row['photo'] ?? null) : null,
                'parent_id' => $row['parent_id'],
            ]);
        }
    }
}
