<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DigitalNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'photo',
        'language_id',
        'category_id',
        'sub_category_id',
        'subject_id',
        'topic_id',
        'content',
        'status',
    ];

    public function language()
    {
        return $this->belongsTo(Language::class);
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
}
