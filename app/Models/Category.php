<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'categories';

    protected $fillable = [
        'name',
        'language_id',
        'photo'
    ];

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function subcategory()
    {
        return $this->hasMany(SubCategory::class);
    }

    public function question()
    {
        return $this->hasMany(Question::class);
    }
}
