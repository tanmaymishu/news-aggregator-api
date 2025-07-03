<?php

use App\Exceptions\NewsSourceException;
use App\Services\News\TheGuardian;
use Illuminate\Support\Facades\Http;

describe('TheGuardian source aggregation pipeline', function () {
    test('normalizing on empty search result throws exception', function () {
        expect(fn () => (new TheGuardian)->normalize())->toThrow(NewsSourceException::class);
    });

    test('unsuccessful response from API throws exception', function () {
        Http::fake([
            'content.guardianapis.com/*' => 500,
        ]);

        expect(fn () => (new TheGuardian)->search(null))->toThrow(NewsSourceException::class);
    });

    test('saving an empty articles collection throws exception', function () {
        expect(fn () => (new TheGuardian)->save())->toThrow(NewsSourceException::class);
    });

    test('an article can be mapped', function () {
        $instance = new TheGuardian;
        $article = $instance->mapArticle([
            'webTitle' => 'Foo',
            'webPublicationDate' => 'Foo',
            'fields' => [
                'headline' => 'bar',
            ],
        ]);

        expect($article['title'])->toBe('Foo');
    });
});
