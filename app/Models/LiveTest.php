<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'language_id',
        'category_id',
        'sub_category_id',
        'mode',
        'title',
        'start_date',
        'end_date',
        'status',
        'question_ids',
        'toppers_star',
        'toppers',
        'participant_star',
    ];

    protected $casts = [
        'sub_category_id' => 'json',
        'question_ids' => 'json',
        'status' => 'boolean',
        'schedule' => 'datetime',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    protected $appends = ['subject_count'];

    public function getSubjectCountAttribute()
    {
        return \App\Models\Subject::whereIn('sub_category_id', (array)$this->sub_category_id)->count();
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function manualQuestions()
    {
        return $this->hasMany(LiveTestManualQuestion::class);
    }
}
