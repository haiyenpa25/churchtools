<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceInvestment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'symbol',
        'type',
        'quantity',
        'buy_price',
        'current_price',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
