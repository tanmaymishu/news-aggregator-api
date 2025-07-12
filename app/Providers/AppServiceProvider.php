<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\News\NewsApi;
use App\Services\News\NewsSource;
use App\Services\News\NYTimes;
use App\Services\News\TheGuardian;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->instance(NewsSource::NEWSAPI_ID, new NewsApi);
        $this->app->instance(NewsSource::NYTIMES_ID, new NYTimes);
        $this->app->instance(NewsSource::THEGUARDIAN_ID, new TheGuardian);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->secure(
                    SecurityScheme::http('bearer')
                );
            });

        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            if (! empty(config('app.frontend_url'))) {
                $exploded = explode('/email/verify', $url);

                $url = config('app.frontend_url').'/email/verify'.$exploded[1];
            }

            return (new MailMessage)
                ->subject('Verify Email Address')
                ->line('Click the button below to verify your email address.')
                ->action('Verify Email Address', $url);
        });
    }
}
