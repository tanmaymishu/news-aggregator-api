<?php

namespace App\Providers;

use App\Services\News\NewsApi;
use App\Services\News\NewsSource;
use App\Services\News\NYTimes;
use App\Services\News\TheGuardian;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->instance(NewsSource::NEWSAPI_ID, new NewsApi());
        $this->app->instance(NewsSource::NYTIMES_ID, new NYTimes());
        $this->app->instance(NewsSource::THEGUARDIAN_ID, new TheGuardian());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
