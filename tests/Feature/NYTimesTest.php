<?php

use App\Exceptions\NewsSourceException;
use App\Services\News\NYTimes;
use Illuminate\Support\Facades\Http;

describe('NYTimes source aggregation pipeline', function () {
    test('normalizing on empty search result throws exception', function () {
        expect(fn () => (new NYTimes)->normalize())->toThrow(NewsSourceException::class);
    });

    test('unsuccessful response from API throws exception', function () {
        Http::fake([
            'api.nytimes.com/*' => 500,
        ]);

        expect(fn () => (new NYTimes)->search(null))->toThrow(NewsSourceException::class);
    });

    test('saving an empty articles collection throws exception', function () {
        expect(fn () => (new NYTimes)->save())->toThrow(NewsSourceException::class);
    });

    test('an article can be mapped', function () {
        $instance = new NYTimes;
        $article = $instance->mapArticle([
            'headline' => ['main' => 'Foo'],
            'pub_date' => 'Foo',
        ]);

        expect($article['title'])->toBe('Foo');
    });
});
