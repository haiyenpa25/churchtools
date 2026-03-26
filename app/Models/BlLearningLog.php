<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlLearningLog extends Model
{
    use HasFactory;

    protected $fillable = ['entity_id', 'event_id', 'user_id', 'last_reviewed_at', 'next_review_at', 'retention_score'];

    protected $casts = [
        'last_reviewed_at' => 'datetime',
        'next_review_at' => 'datetime',
    ];

    public function entity()
    {
        return $this->belongsTo(BlEntity::class, 'entity_id');
    }

    public function event()
    {
        return $this->belongsTo(BlEvent::class, 'event_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
