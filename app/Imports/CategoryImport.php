<?php

namespace App\Imports;

use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CategoryImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $category = Category::find($row['id']);

        if ($category) {
            // Update existing record
            $category->update([
                'name' => $row['name'],
                'language_id' => $row['language_id'],
                'photo' => $row['photo'], // Assume photo is a URL or path
            ]);
            return null; // No need to create a new instance
        } else {
            // Create new record
            return new Category([
                'id' => $row['id'], // Use provided ID
                'name' => $row['name'],
                'language_id' => $row['language_id'],
                'photo' => $row['photo'], // Assume photo is a URL or path
            ]);
        }
    }
}
