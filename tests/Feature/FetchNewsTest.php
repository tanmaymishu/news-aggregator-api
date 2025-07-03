<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

describe('arguments are valid', function () {
    beforeEach(function () {
        Http::fake([
            'newsapi.org/*' => Http::response(['articles' => []], 200),
            'content.guardianapis.com/*' => Http::response(['response' => ['results' => []]], 200),
            'api.nytimes.com/*' => Http::response(['response' => ['docs' => []]], 200),
        ]);
    });
    test('for misspelled sources', function () {
        $output = Artisan::call('news:fetch asdfsdf');
        expect($output)->toBe(0);
    });

    test('for misspelled sources along with correct ones', function () {
        $output = Artisan::call('news:fetch newsapi asdfsdf');
        expect($output)->toBe(0);
    });

    test('when no args provided and fallbacks to default sources ', function () {
        $output = Artisan::call('news:fetch');
        expect($output)->toBe(0);
    });

    test('when all args are incorrect and fallbacks to default sources ', function () {
        $output = Artisan::call('news:fetch foo bar baz');
        expect($output)->toBe(0);
    });
});
