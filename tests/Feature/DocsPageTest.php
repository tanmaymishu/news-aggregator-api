<?php

use App\Console\Commands\FetchNews;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

test('the homepage redirects to the /docs/api page', function () {
    $response = $this->get('/');

    $response->assertStatus(302)->assertRedirect('/docs/api');
});
