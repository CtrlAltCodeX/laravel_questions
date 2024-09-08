<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function question_bank()
    {
        return $this->belongsTo(QuestionBank::class);
    }
}
