<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TranslatedQuestions extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'language_id',
        'question_text',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
