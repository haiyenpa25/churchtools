<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlEdge extends Model
{
    use HasFactory;
    
    protected $fillable = ['source_node_id', 'target_node_id', 'relationship', 'metadata'];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function source() {
        return $this->belongsTo(BlNode::class, 'source_node_id');
    }

    public function target() {
        return $this->belongsTo(BlNode::class, 'target_node_id');
    }
}
