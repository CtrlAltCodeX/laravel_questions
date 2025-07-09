<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingCbt extends Model
{
    use HasFactory;
    protected $table = 'cbts';

      protected $fillable = [
        'language_type',
        'language_ids',
        'category_id',
        'sub_category_id',
        'question_limit',
    ];

    protected $casts = [
        'language_ids' => 'array',
    ];
}
