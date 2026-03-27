<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlQuiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'options',
        'correct_option',
        'explanation',
        'reference',
    ];

    protected $casts = [
        'options' => 'array',
    ];
}
