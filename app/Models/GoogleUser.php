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
        'coins',
        'plan',
        'referral_code',
        'friend_code',
        'status',
        'category_id',
        'language_id',
        'login_date'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // In GoogleUser.php model
public function userCourses()
{
    return $this->hasMany(UserCourse::class, 'user_id');
}


}
