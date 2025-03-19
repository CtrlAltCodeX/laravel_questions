<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScoreBoard extends Model
{
    use HasFactory;
    protected $table = 'score_boards';

    protected $fillable = [
        'google_user_id',
        'sub_category_id',
        'total_videos',
        'quiz_practice',
        'test_rank'
    ];

    public function user()
    {
        return $this->belongsTo(GoogleUser::class, 'google_user_id', 'id');
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }
}
