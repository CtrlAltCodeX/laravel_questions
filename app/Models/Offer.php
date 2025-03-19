<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'offers';

    protected $fillable = [
        'name', 'status', 'subject_id','mode', 'discount', 'valid_until','banner'
     
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
