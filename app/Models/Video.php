<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'v_no', 'thumbnail', 'topic_id', 'description',
        'youtube_link', 'video_id', 'duration', 'video_type', 'pdf_link'
    ];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

   
}
