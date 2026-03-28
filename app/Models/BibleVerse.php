<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BibleVerse extends Model
{
    protected $fillable = ['bible_chapter_id', 'verse_number', 'content'];

    public function chapter()
    {
        return $this->belongsTo(BibleChapter::class, 'bible_chapter_id');
    }
}
