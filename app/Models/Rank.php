<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rank extends Model
{
    protected $fillable = [
        'google_user_id',
        'live_test_id',
        'right_answer',
        'wrong_answer',
        'total_questions',
        'time_taken',
    ];

    public function user()
    {
        return $this->belongsTo(GoogleUser::class , 'google_user_id');
    }

    public function liveTest()
    {
        return $this->belongsTo(LiveTest::class , 'live_test_id');
    }
}