<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'topics';

    protected $fillable = [
        'name',
        'subject_id',
        'photo',
        'status',
        'access'
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function question()
    {
        return $this->hasMany(Question::class);
    }
}
