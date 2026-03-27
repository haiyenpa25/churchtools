<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'era',
        'description',
        'image_url',
        'order_index',
    ];
}
