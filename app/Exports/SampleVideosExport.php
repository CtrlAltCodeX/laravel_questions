<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SampleVideosExport implements FromCollection, WithHeadings
{
    public function __construct() {}

    public function collection()
    {
        return new Collection([
            [
                'id' => '',
                'v_no' => '',
                'language_id' => 1,
                'category_id' => 1,
                'sub_category_id' => 1,
                'subject_id' => 1,
                'topic_id' => 1,
                'name' => '',
                'description' => '',
                'thumbnail' => '',
                'youtube_link' => '',
                'video_link' => '',
                'video_type' => '',
                'pdf_link' => '',
                'duration' => '',
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'id',
            'v_no',
            'language_id',
            'category_id',
            'sub_category_id',
            'subject_id',
            'topic_id',
            'name',
            'description',
            'thumbnail',
            'youtube_link',
            'video_link',
            'video_type',
            'pdf_link',
            'duration',
        ];
    }
}
