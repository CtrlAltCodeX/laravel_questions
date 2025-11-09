<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'subscription_type',
        'valid_from',
        'valid_to',
        'status'
    ];
    public function user()
    {
        return $this->belongsTo(\App\Models\GoogleUser::class, 'user_id');
    }

    public function course()
    {
        return $this->belongsTo(\App\Models\Course::class, 'course_id');
    }
}
