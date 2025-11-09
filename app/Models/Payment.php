<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'amount',
        'currency',
        'status',
        'email',
        'contact',
        'user_id',
        'course_id',
        'method',
        'card_last4',
        'card_network',
        'vpa'
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
