<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceDebt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'partner_name',
        'type',
        'amount',
        'due_date',
        'status',
        'note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
