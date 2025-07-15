<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'sub_categories';

    protected $fillable = [
        'category_id',
        'name',
        'photo',
        'plan_type',
        'plans',
        'status',
        'parent_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subject()
    {
        return $this->hasMany(Subject::class);
    }

    public function question()
    {
        return $this->hasMany(Question::class);
    }
}
