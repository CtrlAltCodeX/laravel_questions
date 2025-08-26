<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Course extends Model
{
    protected $fillable = [
        'name',
        'language_id',
        'category_id',
        'sub_category_id',
        'subject_id',
        'status',
        'subscription',
        'banner',
        'language',
        'question_limit',
        'subject_limit',
        'part_limit'
    ];

    protected $casts = [
        'subscription' => 'array',
        'subject_limit' => 'array',
        'part_limit' => 'array',
        'subject_id' => 'array',
        'sub_category_id' => 'array'
    ];
}