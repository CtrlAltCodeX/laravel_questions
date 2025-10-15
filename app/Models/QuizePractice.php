<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizePractice extends Model
{
    use HasFactory;

    protected $table = 'quize_practice';

    protected $fillable = [
        'google_user_id',
        'subject_id',
        'topic_id',
        'percentage',
    ];

    public function user()
    {
        return $this->belongsTo(GoogleUser::class, 'google_user_id');
    }

    public function subjects()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class, 'topic_id');
    }
}
