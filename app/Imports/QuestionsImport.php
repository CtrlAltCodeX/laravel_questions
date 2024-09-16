<?php

namespace App\Imports;

use App\Models\Question;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class QuestionsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $questionBank = new \App\Models\QuestionBank([
            'language_id' => $this->getLanguageId($row['language']),
            'category_id' => $this->getCategoryId($row['category']),
            'sub_category_id' => $this->getSubCategoryId($row['sub_category']),
            'subject_id' => $this->getSubjectId($row['subject']),
            'topic_id' => $this->getTopicId($row['topic']),
        ]);

        $questions = new Question([
            'question_number' => $row['qno'],
            'question' => $row['question_'.strtolower($questionBank->language->name)],
            'option_a' => $row['option_a_'.strtolower($questionBank->language->name)],
            'option_b' => $row['option_b_'.strtolower($questionBank->language->name)],
            'option_c' => $row['option_c_'.strtolower($questionBank->language->name)],
            'option_d' => $row['option_d_'.strtolower($questionBank->language->name)],
            'answer' => $row['answer'],
            'photo' => $row['photo'],
            'photo_link' => $row['photo_link'],
            'notes' => $row['notes'],
            'level' => $row['level'],
            'question_bank_id' => $questionBank->id,
            'language_id' => $questionBank->language_id,
            'category_id' => $questionBank->category_id,
            'sub_category_id' => $questionBank->sub_category_id,    
            'subject_id' => $questionBank->subject_id,
            'topic_id' => $questionBank->topic_id,
        ]);

        $questionBank->save();
        $questions->save();

        return $questions;
    }

    private function getLanguageId($name)
    {
        return \App\Models\Language::where('name', $name)->first()->id ?? null;
    }

    private function getCategoryId($name)
    {
        return \App\Models\Category::where('name', $name)->first()->id ?? null;
    }

    private function getSubCategoryId($name)
    {
        return \App\Models\SubCategory::where('name', $name)->first()->id ?? null;
    }

    private function getSubjectId($name)
    {
        return \App\Models\Subject::where('name', $name)->first()->id ?? null;
    }

    private function getTopicId($name)
    {
        return \App\Models\Topic::where('name', $name)->first()->id ?? null;
    }
}