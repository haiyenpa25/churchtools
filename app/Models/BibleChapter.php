<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BibleChapter extends Model
{
    protected $fillable = ['bible_book_id', 'chapter_number', 'verse_count'];

    public function book()
    {
        return $this->belongsTo(BibleBook::class, 'bible_book_id');
    }

    public function verses()
    {
        return $this->hasMany(BibleVerse::class);
    }
}
