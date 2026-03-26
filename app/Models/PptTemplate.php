<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PptTemplate extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'file_path', 'logo_path', 'status'];

    public function presets()
    {
        return $this->hasMany(PptPreset::class, 'template_id');
    }
}
