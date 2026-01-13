<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoProgress extends Model
{
    use HasFactory;

    protected $table = 'video_progress';

    protected $fillable = [
        'user_id',
        'video_id',
        'watched_seconds',
        'video_total_seconds',
    ];

    public function videos()
    {
        return $this->belongsToMany(Video::class);
    }
}
