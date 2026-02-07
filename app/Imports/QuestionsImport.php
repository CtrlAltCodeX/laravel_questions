<?php

namespace App\Imports;

use App\Models\Language;
use App\Models\Question;
use App\Models\TranslatedQuestions;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class QuestionsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $headings = array_keys($row);
        $substring = "language";
        $languageCount = 0;
        $languageIds = [];

        foreach ($headings as $key) {
            if (strpos($key, $substring) !== false) {
                $languageCount++;
                $id = str_replace($substring . '_', '', $key);
                $languageIds[] = $id;
            }
        }

        // Get category, sub_category, subject, and topic IDs
        // $categoryId = $this->getCategoryId($row['category']);
        // $subCategoryId = $this->getSubCategoryId($row['subcategory']);
        // $subjectId = $this->getSubjectId($row['subject']);
        // $topicId = $this->getTopicId($row['topic']);

        $categoryId = $row['category'];
        $subCategoryId = $row['subcategory'];
        $subjectId = $row['subject'];
        $topicId = $row['topic'];

        $question = Question::find($row['id']);

        if (!$question) {
            $question = Question::create([
                'question_number' => $row['qno'],
                'question' => $row['question'],
                'option_a' => $row['option_a'],
                'option_b' => $row['option_b'],
                'option_c' => $row['option_c'],
                'option_d' => $row['option_d'],
                'answer' => $row['answer'],
                'photo' => $row['photo'],
                'photo_link' => $row['photo_link'],
                'notes' => $row['notes'],
                'level' => $row['level'],
                'language_id' => $row['language_id'],
                'category_id' => $categoryId,
                'sub_category_id' => $subCategoryId,
                'subject_id' => $subjectId,
                'topic_id' => $topicId,
                'exam_years' => $row['exam_years'] ?? null,
            ]);
        } else {
            $question->update([
                'question_number' => $row['qno'],
                'question' => $row['question'],
                'option_a' => $row['option_a'],
                'option_b' => $row['option_b'],
                'option_c' => $row['option_c'],
                'option_d' => $row['option_d'],
                'answer' => $row['answer'],
                'photo' => $row['photo'],
                'photo_link' => $row['photo_link'],
                'notes' => $row['notes'],
                'level' => $row['level'],
                'language_id' => $row['language_id'],
                'category_id' => $categoryId,
                'sub_category_id' => $subCategoryId,
                'subject_id' => $subjectId,
                'topic_id' => $topicId,
                'exam_years' => $row['exam_years'] ?? null,
            ]);
        }

        // Create the question based on the provided data

        return $question;
    }
}
