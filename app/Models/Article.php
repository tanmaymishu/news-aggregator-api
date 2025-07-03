<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

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

    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * Get articles based on user's saved preferences
     */
    #[Scope]
    protected function preferred(Builder $query, ?Preference $preference): Builder
    {
        // If the user has not yet set a preference, skip filtering.
        if (empty($preference)) {
            return $query;
        }

        return $query->where(function ($query) use ($preference) {
            if ($preference->sources) {
                $query->whereIn('source', $preference->sources);
            }

            if ($preference->categories) {
                $query->orWhereIn('category', $preference->categories);
            }

            if ($preference->authors) {
                $query->orWhereIn('author', $preference->authors);
            }
        });
    }
}
