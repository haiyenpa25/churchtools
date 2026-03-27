<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlImportedFile extends Model
{
    protected $table = 'bl_imported_files';

    protected $fillable = [
        'category',
        'file_name',
        'file_hash',
        'status',
        'total_chunks',
        'processed_chunks',
        'nodes_added',
        'edges_added',
        'error_log',
    ];
}
