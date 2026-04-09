<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'image',
        'type',
        'source',
        'is_read',
        'link_title',
        'link_url',
        'action_url',
        'schedule_at',
        'sent_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'schedule_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

}