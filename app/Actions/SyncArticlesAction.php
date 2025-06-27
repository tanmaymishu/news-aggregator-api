<?php

namespace App\Actions;

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use Illuminate\Support\Facades\Cache;

class SyncArticlesAction
{
    public function handle(array $attributes)
    {
        Article::query()->upsert(
            $attributes,
            ['web_url'],
            ['title', 'category', 'source', 'description', 'content', 'author', 'web_url', 'featured_image_url'],
        );

        Source::query()->upsert(
            collect($attributes)->pluck('source')->map(fn ($source) => ['name' => $source])->toArray(),
            ['name'],
            ['name'],
        );

        Author::query()->upsert(
            collect($attributes)->pluck('author')->map(fn ($author) => ['name' => $author])->toArray(),
            ['name'],
            ['name'],
        );

        Category::query()->upsert(
            collect($attributes)->pluck('category')->map(fn ($category) => ['name' => $category])->toArray(),
            ['name'],
            ['name'],
        );

        Cache::remember('articles', now()->addHour(), fn () => Article::simplePaginate());
        Cache::remember('sources', now()->addHour(), fn () => Source::all());
        Cache::remember('categories', now()->addHour(), fn () => Category::all());
        Cache::remember('authors', now()->addHour(), fn () => Author::all());
    }
}
