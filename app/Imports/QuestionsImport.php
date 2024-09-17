<?php

namespace App\Imports;

use App\Models\Question;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class QuestionsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Get language, category, sub_category, subject, and topic IDs
        $languageId = $this->getLanguageId($row['language']);
        $categoryId = $this->getCategoryId($row['category']);
        $subCategoryId = $this->getSubCategoryId($row['sub_category']);
        $subjectId = $this->getSubjectId($row['subject']);
        $topicId = $this->getTopicId($row['topic']);

        // Check if any of the IDs are missing and return a specific message
        if (!$languageId) {
            return back()->with('error', 'Language not available.');
        }
        if (!$categoryId) {
            return back()->with('error', 'Category not available.');
        }
        if (!$subCategoryId) {
            return back()->with('error', 'Subcategory not available.');
        }
        if (!$subjectId) {
            return back()->with('error', 'Subject not available.');
        }
        if (!$topicId) {
            return back()->with('error', 'Topic not available.');
        }

        // Create a new question bank entry
        $questionBank = new \App\Models\QuestionBank([
            'language_id' => $languageId,
            'category_id' => $categoryId,
            'sub_category_id' => $subCategoryId,
            'subject_id' => $subjectId,
            'topic_id' => $topicId,
        ]);

        // Create the question based on the provided data
        $questions = new Question([
            'question_number' => $row['qno'],
            'question' => $row['question_' . strtolower($questionBank->language->name)],
            'option_a' => $row['option_a_' . strtolower($questionBank->language->name)],
            'option_b' => $row['option_b_' . strtolower($questionBank->language->name)],
            'option_c' => $row['option_c_' . strtolower($questionBank->language->name)],
            'option_d' => $row['option_d_' . strtolower($questionBank->language->name)],
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

        // Save both QuestionBank and Question
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
