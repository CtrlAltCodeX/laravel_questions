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
        // Get all the headings and count the number of languages
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

        // Create the question based on the provided data
        $question = Question::updateOrCreate([
            'question_number' => $row['qno'],
            'question' => $row['question_' . strtolower(Language::findOrFail($languageIds[0])->name)],
            'option_a' => $row['option_a_' . strtolower(Language::findOrFail($languageIds[0])->name)],
            'option_b' => $row['option_b_' . strtolower(Language::findOrFail($languageIds[0])->name)],
            'option_c' => $row['option_c_' . strtolower(Language::findOrFail($languageIds[0])->name)],
            'option_d' => $row['option_d_' . strtolower(Language::findOrFail($languageIds[0])->name)],
            'answer' => $row['answer'],
            'photo' => $row['photo'],
            'photo_link' => $row['photo_link'],
            'notes' => $row['notes'],
            'level' => $row['level'],
            'language_id' => $languageIds[0],
            'category_id' => $categoryId,
            'sub_category_id' => $subCategoryId,
            'subject_id' => $subjectId,
            'topic_id' => $topicId,
        ]);

        // foreach ($languageIds as $lang_id) {
        //     $language = Language::findOrFail($lang_id);
        //     # code...
        //     TranslatedQuestions::updateOrCreate(
        //         [
        //             'question_id' => $question->id,
        //             'language_id' => $lang_id,
        //         ],
        //         [
        //         'question_id' => $question->id,
        //         'language_id' => $lang_id,
        //         'question_text' => $row['question_' . strtolower($language->name)],
        //         'option_a' => $row['option_a_' . strtolower($language->name)],
        //         'option_b' => $row['option_b_' . strtolower($language->name)],
        //         'option_c' => $row['option_c_' . strtolower($language->name)],
        //         'option_d' => $row['option_d_' . strtolower($language->name)],
        //     ]);
        // }

        return $question;
    }
}
