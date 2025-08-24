<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionBankCount extends Model
{
    use HasFactory;

    protected $table = 'question_bank_count';

    protected $fillable = [
        'google_user_id',
        'subject_id',
        'topic_id',
        'count',
    ];

   
    public function user()
    {
        return $this->belongsTo(GoogleUser::class, 'google_user_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class, 'topic_id');
    }
}
