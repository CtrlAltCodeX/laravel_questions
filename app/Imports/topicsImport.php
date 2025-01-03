<?php

namespace App\Imports;

use App\Models\Topic;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
class topicsImport implements ToModel, WithHeadingRow
{
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