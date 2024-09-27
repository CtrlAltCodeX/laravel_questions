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
        'question',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'answer'
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
