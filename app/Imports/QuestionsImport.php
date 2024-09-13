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
            'question' => $row['question'],
            'option_a' => $row['option_a'],
            'option_b' => $row['option_b'],
            'option_c' => $row['option_c'],
            'option_d' => $row['option_d'],
            'answer' => $row['answer'],
            'photo' => $row['photo'],
            'photo_link' => $row['photolink'],
            'notes' => $row['notes'],
            'level' => $row['level'],
        ]);

        return $questionBank->save() && $questions->save();
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