<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'profile_image',
        'phone_number',
        'login_type',
        'plan',
        'referral_code',
        'friend_code',
        'status',
        'category_id',
        'language_id',
        'otp'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }


    public function coinsHistory()
    {
        return $this->hasMany(UserCoin::class, 'user_id');
    }

    // total coins getter
    public function getTotalCoinsAttribute()
    {
        return $this->coinsHistory()->sum('coin');
    }



    // In GoogleUser.php model
    public function userCourses()
    {
        return $this->hasMany(UserCourse::class, 'user_id');
    }
}
