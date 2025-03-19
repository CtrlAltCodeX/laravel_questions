<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'google_user_id', 'coin', 'method', 'date', 'transaction_id', 'amount','status','payment_type'
    ];


    public function user()
    {
        return $this->belongsTo(GoogleUser::class, 'google_user_id', 'id');
    }
}
