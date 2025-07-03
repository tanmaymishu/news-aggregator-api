<?php

use App\Actions\SyncArticlesAction;
use App\Models\Article;
use Illuminate\Support\Facades\Cache;

test('SyncArticleAction can sync the articles', function () {
    $instance = new SyncArticlesAction;

    $instance->handle(
        [
            [
                'title' => fake()->sentence,
                'category' => fake()->word,
                'source' => fake()->word,
                'description' => fake()->sentence,
                'content' => fake()->paragraph,
                'author' => fake()->name,
                'web_url' => fake()->unique()->url,
                'featured_image_url' => fake()->unique()->url,
                'published_at' => now()->toDateString(),
            ],
            [
                'title' => fake()->sentence,
                'category' => fake()->word,
                'source' => fake()->word,
                'description' => fake()->sentence,
                'content' => fake()->paragraph,
                'author' => fake()->name,
                'web_url' => fake()->unique()->url,
                'featured_image_url' => fake()->unique()->url,
                'published_at' => now()->toDateString(),
            ],
        ]
    );

    $cached = Cache::get('articles');
    expect(count($cached))->toBe(2);
    $this->assertDatabaseCount(Article::class, 2);
    $this->assertDatabaseCount(Article::class, 2);
});
