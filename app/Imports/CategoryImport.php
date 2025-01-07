<?php

namespace App\Imports;

use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\WithValidation;

class CategoryImport implements ToModel, WithHeadingRow, SkipsOnError, WithValidation
{
    use SkipsErrors;

    public $validationErrors = []; 

    public function rules(): array
    {
        return [
            'id' => 'required|integer',
            'name' => 'required|string|max:255',
            'language_id' => 'required|integer',
            'photo' => 'nullable|string|max:255',
        ];
    }

    public function model(array $row)
    {
        // Validation happens automatically before this method is called
        $category = Category::find($row['id']);
        if ($category) {
            $category->update([
                'name' => $row['name'],
                'language_id' => $row['language_id'],
                'photo' => $row['photo'],
            ]);
            return null;
        } else {
            return new Category([
                'id' => $row['id'],
                'name' => $row['name'],
                'language_id' => $row['language_id'],
                'photo' => $row['photo'],
            ]);
        }
    }
}
