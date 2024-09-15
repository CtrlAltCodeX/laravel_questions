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

            $formattedQuestion[] = $question['language'];
            $formattedQuestion[] = $question['category'];
            $formattedQuestion[] = $question['subCategory'];
            $formattedQuestion[] = $question['subject'];    
            $formattedQuestion[] = $question['topic'];
            $formattedQuestion[] = $question['qno'];

            // Add questions for each language
            foreach ($this->languages as $languageId) {
                $formattedQuestion[] = $question['question'][$languageId] ?? '';
            }

            // Add options for each language
            foreach (['option_a', 'option_b', 'option_c', 'option_d'] as $option) {
                foreach ($this->languages as $languageId) {
                    $formattedQuestion[] = $question[$option][$languageId] ?? '';
                }
            }

            // Add other fields
            $formattedQuestion[] = $question['answer'];
            $formattedQuestion[] = $question['notes'];
            $formattedQuestion[] = $question['level'];

            $formattedQuestions[] = $formattedQuestion;
        }

        return new Collection($formattedQuestions);
    }

    public function headings(): array
    {
        $headings = [];

        $headings[] = 'Language';
        $headings[] = 'Category';
        $headings[] = 'Sub Category';
        $headings[] = 'Subject';
        $headings[] = 'Topic';
        $headings[] = 'Q.No';

        // Add headings for questions in each language
        foreach ($this->languages as $languageId) {
            $language = Language::find($languageId);
            $headings[] = 'Question (' . $language->name . ')';
        }

        // Add headings for options in each language
        foreach (['Option A', 'Option B', 'Option C', 'Option D'] as $option) {
            foreach ($this->languages as $languageId) {
                $language = Language::find($languageId);
                $headings[] = $option . ' (' . $language->name . ')';
            }
        }

        // Add headings for other fields
        $headings[] = 'Answer';
        $headings[] = 'Notes';
        $headings[] = 'Level';

        return $headings;
    }
}