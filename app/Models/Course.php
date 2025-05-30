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
        'banner'
    ];

    protected $casts = [
        'subscription' => 'array',
    ];
}