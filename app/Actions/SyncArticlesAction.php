<?php

namespace App\Actions;

use App\Models\Article;

class SyncArticlesAction
{
    public function handle(array $attributes)
    {
        Article::query()->upsert(
            $attributes,
            ['web_url'],
            ['title', 'category', 'source', 'description', 'content', 'author', 'web_url', 'featured_image_url'],
        );
    }
}
