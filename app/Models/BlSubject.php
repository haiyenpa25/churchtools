<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlSubject extends Model
{
    use HasFactory;

    protected $fillable = ['parent_id', 'name', 'type'];

    public function parent()
    {
        return $this->belongsTo(BlSubject::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(BlSubject::class, 'parent_id');
    }

    public function events()
    {
        return $this->hasMany(BlEvent::class, 'subject_id');
    }
}
