<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlFlashcard extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'answer',
        'reference',
        'tags',
        'status',
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    public function reviews()
    {
        return $this->hasMany(BlFlashcardReview::class, 'flashcard_id');
    }
}
