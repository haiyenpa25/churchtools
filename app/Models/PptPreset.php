<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PptPreset extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id', 'x', 'y', 'width', 'height', 'font_config', 'is_green_screen',
    ];

    protected $casts = [
        'font_config' => 'array',
        'is_green_screen' => 'boolean',
    ];

    public function template()
    {
        return $this->belongsTo(PptTemplate::class, 'template_id');
    }
}
