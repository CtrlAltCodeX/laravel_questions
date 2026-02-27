<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveTestManualQuestion extends Model
{
    protected $fillable = [
        'live_test_id',
        'language_id',
        'category_id',
        'sub_category_id',
        'subject_id',
        'question',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'answer',
        'photo',
    ];

    public function liveTest()
    {
        return $this->belongsTo(LiveTest::class);
    }
}
