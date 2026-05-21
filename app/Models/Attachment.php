<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable = [
        'post_id',
        'filename',
        'original_name',
        'mime_type',
        'size',
        'path',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
