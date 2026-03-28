<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BibleBook extends Model
{
    protected $fillable = ['name', 'book_number', 'chapter_count'];

    public function chapters()
    {
        return $this->hasMany(BibleChapter::class);
    }
}
