<?php

namespace App\Exports;

use App\Models\Language;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class QuestionsExport implements FromCollection, WithHeadings
{
    protected $questions;
    protected $languages;

    public function __construct(array $questions, array $languages)
    {
        $this->questions = $questions;
        $this->languages = $languages;
    }

    public function collection()
    {
        $formattedQuestions = [];

        foreach ($this->questions as $question) {
            $formattedQuestion = [];

            // Add question number
            $formattedQuestion[] = '';
            $formattedQuestion[] = $question['qno'];

            // Add language names
            // foreach ($this->languages as $languageId) {
            //     $formattedQuestion[] = $question['language'][$languageId] ?? '';
            // }

            // Add category, subCategory, subject, and topic
            $formattedQuestion[] = $question['language_id'];
            $formattedQuestion[] = $question['category'];
            $formattedQuestion[] = $question['subCategory'];
            $formattedQuestion[] = $question['subject'];
            $formattedQuestion[] = $question['topic'];
            $formattedQuestion[] = $question['question'];
            $formattedQuestion[] = $question['option_a'];
            $formattedQuestion[] = $question['option_b'];
            $formattedQuestion[] = $question['option_c'];
            $formattedQuestion[] = $question['option_d'];

            // Add questions for each language
            // foreach ($this->languages as $languageId) {
            //     $formattedQuestion[] = $question['question'][$languageId] ?? '';
            // }

            // // Add options for each language
            // foreach (['option_a', 'option_b', 'option_c', 'option_d'] as $option) {
            //     foreach ($this->languages as $languageId) {
            //         $formattedQuestion[] = $question[$option][$languageId] ?? '';
            //     }
            // }

            // Add other fields
            $formattedQuestion[] = $question['answer'];
            $formattedQuestion[] = $question['notes'];
            $formattedQuestion[] = $question['level'];
            $formattedQuestion[] = $question['photo'];
            $formattedQuestion[] = $question['photo_link'];

            $formattedQuestions[] = $formattedQuestion;
        }

        return new Collection($formattedQuestions);
    }

    public function headings(): array
    {
        $headings = [];

        // Add language headings
        // foreach ($this->languages as $languageId) {
        //     $headings[] = "language_id";
        // }

        // Add static headings
        $headings = array_merge($headings, [
            'id',
            'qno ',
            'language_id',
            'category',
            'subcategory',
            'subject',
            'topic'
        ]);

        // Add question headings for each language
        // foreach ($this->languages as $languageId) {
        //     $languageName = Language::findOrFail($languageId)->name;
        //     $headings[] = "question";
        // }

        // // Add option headings for each language
        // foreach (['option_a', 'option_b', 'option_c', 'option_d'] as $option) {
        //     foreach ($this->languages as $languageId) {
        //         $languageName = Language::findOrFail($languageId)->name;
        //         $headings[] = "$option";
        //     }
        // }

        $columns = ['question', 'option_a', 'option_b', 'option_c', 'option_d', 'answer', 'notes', 'level', 'photo', 'photo_link'];

        // Add other field headings
        $headings = array_merge($headings, $columns);

        return $headings;
    }
}
