<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MockTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'google_user_id',
        'sub_category_id',
        'right_answer',
        'wrong_answer',
        'total_questions',
        'time_taken',
    ];

    public function user()
    {
        return $this->belongsTo(GoogleUser::class, 'google_user_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }
}
