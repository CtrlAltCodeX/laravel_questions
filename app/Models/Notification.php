<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'image',
        'type',
        'schedule_at',
        'sent_at',
    ];

    protected $casts = [
        'schedule_at' => 'datetime',
        'sent_at' => 'datetime',
    ];
}
