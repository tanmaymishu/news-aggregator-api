<?php

namespace App\Providers;

use App\News\NewsApi;
use App\News\NewsSource;
use App\News\NYTimes;
use App\News\TheGuardian;
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
