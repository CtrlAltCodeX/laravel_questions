<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizSetting extends Model
{
    use HasFactory;

    protected $table = 'quiz_settings';

    protected $fillable = [
        'user_id',
        'course_id',
        'quiz_limit',
        'timer',
        'auto_next',
        'sound',
        'shuffle',
    ];

    protected $casts = [
        'auto_next' => 'boolean',
        'sound' => 'boolean',
        'shuffle' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(GoogleUser::class, 'user_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
