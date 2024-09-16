<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'level',
        'category_id',
        'sub_category_id',
        'subject_id',
        'topic_id',
        'language_id',
        'qno',
        'notes',
        'photo',
        'photo_link',
        'question',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
    ];


    public function question_bank()
    {
        return $this->belongsTo(QuestionBank::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
