<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BibleCommentary extends Model
{
    protected $guarded = [];

    protected $casts = [
        'raw_data' => 'array',
    ];

    public function book()
    {
        return $this->belongsTo(BibleBook::class, 'bible_book_id');
    }
}
