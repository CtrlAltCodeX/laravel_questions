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
    'name', 'status', 'discount', 'banner',
    'course', 'subscription', 'upgrade', 'valid_from', 'valid_to'
];

  


    public function language()
{
    return $this->belongsTo(Language::class);
}

public function category()
{
    return $this->belongsTo(Category::class);
}

public function subCategory()
{
    return $this->belongsTo(SubCategory::class);
}

public function subject()
{
    return $this->belongsTo(Subject::class);
}



    public function question()
    {
        return $this->hasMany(Question::class);
    }
}
