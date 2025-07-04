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
];

    public function course()
{
    return $this->belongsTo(Course::class);
}

}
