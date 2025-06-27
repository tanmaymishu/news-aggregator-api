<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'category',
        'source',
        'title',
        'description',
        'content',
        'web_url',
        'featured_image_url',
        'author',
        'published_at',
    ];
}
