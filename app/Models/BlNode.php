<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlNode extends Model
{
    use HasFactory;

    protected $fillable = ['label', 'group', 'description', 'metadata'];

    protected $casts = [
        'metadata' => 'array',
    ];
}
