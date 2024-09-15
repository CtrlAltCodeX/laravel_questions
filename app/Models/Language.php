<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function questionsBank(){
        return $this->hasMany(QuestionBank::class);
    }

    public function questions(){
        return $this->hasManyThrough(Question::class, QuestionBank::class);
    }
}
