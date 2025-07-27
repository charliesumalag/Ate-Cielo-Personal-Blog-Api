<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    protected $fillable = [
        'category',
        'title',
        'slug',
        'description',
        'tags',
        'image_path',
        'published',
        'published_at'
    ];

    // RELATIONSHIPS
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comments::class)->whereNull('parent_id')->orderBy('created_at', 'desc');
    }

    public function likes()
    {
        return $this->hasMany(PostLike::class);
    }
}
