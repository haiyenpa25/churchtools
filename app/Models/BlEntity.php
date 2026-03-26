<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlEntity extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'entity_type', 'metadata'];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function events()
    {
        return $this->belongsToMany(BlEvent::class, 'bl_entity_events', 'entity_id', 'event_id')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function learningLogs()
    {
        return $this->hasMany(BlLearningLog::class, 'entity_id');
    }
}
