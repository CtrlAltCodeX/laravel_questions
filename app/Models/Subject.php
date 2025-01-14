<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'subjects';

    protected $fillable = [
        'sub_category_id',
        'name',
        'photo',
        'parent_id',
    ];

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function question()
    {
        return $this->hasMany(Question::class);
    }
}
