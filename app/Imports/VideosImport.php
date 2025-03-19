<?php

namespace App\Imports;

use App\Models\Video;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\WithValidation;

class VideosImport implements ToModel, WithHeadingRow, SkipsOnError, WithValidation
{
    use SkipsErrors;

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'v_no' => 'required|string|max:50',
            'thumbnail' => 'nullable|string|max:255',
            'topic_id' => 'required|integer',
            'description' => 'nullable|string',
            'youtube_link' => 'nullable|string|max:255',
            'video_id' => 'nullable|string|max:255',
            'video_type' => 'nullable|string|max:50',
            'pdf_link' => 'nullable|string|max:255',
        ];
    }

    public function model(array $row)
    {
        $video = Video::find($row['id']);

        if ($video) {
            $video->update([
                'name' => $row['name'],
                'v_no' => $row['v_no'],
                'thumbnail' => $row['thumbnail'],
                'topic_id' => $row['topic_id'],
                'description' => $row['description'],
                'youtube_link' => $row['youtube_link'],
                'video_id' => $row['video_id'],
                'video_type' => $row['video_type'],
                'pdf_link' => $row['pdf_link'],
            ]);
            return null;
        } else {
            return new Video([
                'name' => $row['name'],
                'v_no' => $row['v_no'],
                'thumbnail' => $row['thumbnail'],
                'topic_id' => $row['topic_id'],
                'description' => $row['description'],
                'youtube_link' => $row['youtube_link'],
                'video_id' => $row['video_id'],
                
                'video_type' => $row['video_type'],
                'pdf_link' => $row['pdf_link'],
            ]);
        }
    }
}
