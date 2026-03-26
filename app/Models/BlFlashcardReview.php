<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlFlashcardReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'flashcard_id',
        'ease_factor',
        'interval',
        'next_review_date',
    ];

    protected $casts = [
        'next_review_date' => 'datetime',
    ];

    public function flashcard()
    {
        return $this->belongsTo(BlFlashcard::class, 'flashcard_id');
    }
}
