<?php

use App\Exceptions\NewsSourceException;
use App\Services\News\NewsApi;
use Illuminate\Support\Facades\Http;

describe('NewsApi source aggregation pipeline', function () {
    test('normalizing on empty search result throws exception', function () {
        expect(fn () => (new NewsApi)->normalize())->toThrow(NewsSourceException::class);
    });

    test('unsuccessful response from API throws exception', function () {
        Http::fake([
            'newsapi.org/*' => 500,
        ]);

        expect(fn () => (new NewsApi)->search(null))->toThrow(NewsSourceException::class);
    });

    test('saving an empty articles collection throws exception', function () {
        expect(fn () => (new NewsApi)->save())->toThrow(NewsSourceException::class);
    });

    test('an article can be mapped', function () {
        $instance = new NewsApi;
        $article = $instance->mapArticle([
            'title' => 'Foo',
            'description' => 'Bar',
            'publishedAt' => 'Bar',
        ]);

        expect($article['title'])->toBe('Foo');
    });
});
