<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class QuestionsExport implements FromCollection, WithHeadings
{
    protected $questions;

    public function __construct(array $questions)
    {
        $this->questions = $questions;
    }

    public function collection()
    {
        return new Collection($this->questions);
    }

    public function headings(): array
    {
        return [
            'Question', 
            'Option A', 
            'Option B', 
            'Option C', 
            'Option D', 
            'Answer',
            'Photo', 
            'PhotoLink', 
            'Notes', 
            'Level', 
            'Language', 
            'Category', 
            'Sub Category', 
            'Subject', 
            'Topic'
        ];
    }

    public function map($question): array
    {
        return [
            $question->question,
            $question->option_a,
            $question->option_b,
            $question->option_c,
            $question->option_d,
            $question->answer,
            $question->level,
            $question->language,
            $question->category,
            $question->sub_category,
            $question->subject,
            $question->topic
        ];
    }
}