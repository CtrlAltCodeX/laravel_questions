<?php

namespace App\Imports;

use App\Models\Topic;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\WithValidation;


class topicsImport implements ToModel, WithHeadingRow, SkipsOnError, WithValidation
{

    use SkipsErrors;

    public $validationErrors = []; 

    public function rules(): array
    {
        return [
            'id' => 'required|integer',
            'name' => 'required|string|max:255',
            'subject_id' => 'required|integer',
            'photo' => 'nullable|string|max:255',
        ];
    }


    public function model(array $row)
    {
    $Topic = Topic::find($row['id']);

    if ($Topic) {
      
        $Topic->update([
            'name' => $row['name'],
            'subject_id' => $row['subject_id'],
            'photo' => $row['photo'], 
        ]);
        return null;
    } else {
        // Create new record
        return new Topic([
            'id' => $row['id'], 
            'name' => $row['name'],
            'subject_id' => $row['subject_id'],
            'photo' => $row['photo'],
        ]);
    }

}
}