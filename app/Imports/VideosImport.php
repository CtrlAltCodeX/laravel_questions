<?php

namespace App\Imports;

use App\Models\Video;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Storage;

class VideosImport implements ToModel, WithHeadingRow, SkipsOnError, WithValidation
{
    use SkipsErrors;

    public function rules(): array
    {
        return [
            'id' => 'nullable',
            'name' => 'required|string|max:255',
            'duration' => 'required',
            'v_no' => 'required|max:50',
            'language_id' => 'required|integer',
            'category_id' => 'required|integer',
            'sub_category_id' => 'required|integer',
            'subject_id' => 'required|integer',
            'topic_id' => 'required|integer',
            'description' => 'nullable|string',
            'youtube_link' => 'nullable|string|max:255',
            'video_type' => 'nullable|string|max:50',
            'video_name' => 'required',
            'pdf_link'    => 'required'
        ];
    }

    public function model(array $row)
    {
        $video = Video::find($row['id']);

        if ($video) {
            $path = $row['language_id'] . "/" . $row['category_id'] . "/" . $row['sub_category_id'] . "/" . $row['subject_id'] . "/" . $row['topic_id'];
            $videoFileName = $path . '/videos/' . $row['video_name'];

            Storage::disk('minio')->put($videoFileName, '');

            $video->update([
                'name' => $row['name'],
                'v_no' => $row['v_no'],
                'duration' => $row['duration'],
                'topic_id' => $row['topic_id'],
                'description' => $row['description'],
                'youtube_link' => $row['youtube_link'],
                'video_type' => $row['video_type'],
                'video_link' => $videoFileName,
                'sub_category_id' => $row['sub_category_id'],
                'subject_id' => $row['subject_id'],
            ]);
            return null;
        } else {
            $path = $row['language_id'] . "/" . $row['category_id'] . "/" . $row['sub_category_id'] . "/" . $row['subject_id'] . "/" . $row['topic_id'];
            $videoFileName = $path . '/videos/' . $row['video_name'];

            Storage::disk('minio')->put($videoFileName, '');

            Video::create([
                'name' => $row['name'],
                'v_no' => $row['v_no'],
                'duration' => $row['duration'],
                'topic_id' => $row['topic_id'],
                'description' => $row['description'],
                'youtube_link' => $row['youtube_link'],
                'video_type' => $row['video_type'],
                'video_link' => $videoFileName,
                'sub_category_id' => $row['sub_category_id'],
                'subject_id' => $row['subject_id'],
            ]);
        }
    }
}
