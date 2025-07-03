<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'image',
        'published',
        'published_at',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
