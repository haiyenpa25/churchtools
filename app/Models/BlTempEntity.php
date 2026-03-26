<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlTempEntity extends Model
{
    use HasFactory;

    protected $table = 'bl_temp_entities';

    protected $fillable = [
        'type',        // flashcard, event, timeline...
        'title',
        'description',
        'raw_data',    // JSON data from Gemini
        'status',      // pending, approved, rejected
    ];

    protected $casts = [
        'raw_data' => 'array',
    ];
}
