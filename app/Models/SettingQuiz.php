<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingQuiz extends Model
{
    use HasFactory;

        protected $table = 'quizzes';


      protected $fillable = [
        'language_type',
        'language_ids',
        'category_id',
        'sub_category_id',
        'question_limit',
    ];

    protected $casts = [
        'language_ids' => 'array',
    ];
}
